<?php

namespace App\Actions\Transaction;

use App\Enums\ShippingStatus;
use App\Enums\ShippingType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateOrderAction
{
    public function execute(string $userId, int $shippingFee, ?string $notes = null, int $adminFee = 0, array $shippingAddress = [], ?string $shippingType = null): array
    {
        // Cegah double click dengan Cache Lock (10 detik)
        $lock = Cache::lock("create_order_lock_{$userId}", 10);

        if (!$lock->get()) {
            return ['success' => false, 'message' => 'Order is being processed, please wait.'];
        }

        DB::beginTransaction();
        try {
            // Ambil data keranjang beserta snapshot harga
            $cartItems = DB::table('carts')
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Shopping cart is empty.');
            }

            // Hitung subtotal
            $subtotalAmount = 0;
            foreach ($cartItems as $item) {
                $subtotalAmount += ($item->price_snapshot * $item->quantity);
            }

            $totalPayment = $subtotalAmount + $shippingFee + $adminFee;
            $orderId = (string) Str::uuid();

            $shippingType = ShippingType::tryFrom((string) $shippingType)?->value
                ?? ShippingType::ONLINE_COURIER->value;

            // Insert ke tabel orders
            $orderData = [
                'id' => $orderId,
                'customer_id' => $userId,
                'subtotal_amount' => $subtotalAmount,
                'shipping_fee' => $shippingFee,
                'total_payment' => $totalPayment,
                'notes' => $notes,
                'order_status' => 'PENDING_PAYMENT',
                'created_at' => now(),
                'order_date' => now(),
            ];

            if (Schema::hasColumn('orders', 'shipping_type')) {
                $orderData['shipping_type'] = $shippingType;
            }

            if (Schema::hasColumn('orders', 'shipping_status')) {
                $orderData['shipping_status'] = ShippingStatus::AWAITING_PICKUP->value;
            }

            DB::table('orders')->insert($orderData);

            if (!empty($shippingAddress)) {
                $coordinates = $shippingAddress['coordinates'] ?? null;
                $latitude = null;
                $longitude = null;

                if (is_array($coordinates)) {
                    $latitude = $coordinates['latitude'] ?? $coordinates['lat'] ?? null;
                    $longitude = $coordinates['longitude'] ?? $coordinates['longtitude'] ?? $coordinates['lng'] ?? $coordinates['lon'] ?? null;
                    $coordinates = json_encode($coordinates);
                } elseif (is_string($coordinates)) {
                    $decoded = json_decode($coordinates, true);
                    if (is_array($decoded)) {
                        $latitude = $decoded['latitude'] ?? $decoded['lat'] ?? null;
                        $longitude = $decoded['longitude'] ?? $decoded['longtitude'] ?? $decoded['lng'] ?? $decoded['lon'] ?? null;
                    }
                }

                DB::table('order_addresses')->insert([
                    'id' => (string) Str::uuid(),
                    'order_id' => $orderId,
                    'recipient_name' => $shippingAddress['recipient_name'] ?? '',
                    'recipient_phone' => $shippingAddress['recipient_phone'] ?? '',
                    'shipping_address' => $shippingAddress['address'] ?? '',
                    'city' => $shippingAddress['city'] ?? '',
                    'postal_code' => $shippingAddress['postal_code'] ?? '',
                    'coordinates' => $coordinates,
                    'latitude' => is_numeric($latitude) ? (float) $latitude : null,
                    'longitude' => is_numeric($longitude) ? (float) $longitude : null,
                ]);
            }

            // Siapkan array untuk bulk insert order_items
            $orderItemsData = [];
            foreach ($cartItems as $item) {
                $orderItemsData[] = [
                    'id' => (string) Str::uuid(),
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'cake_name_snapshot' => $item->cake_name_snapshot,
                    'price_snapshot' => $item->price_snapshot,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price_snapshot * $item->quantity,
                ];
            }

            DB::table('order_items')->insert($orderItemsData);

            // Hapus isi keranjang (otomatis terhapus jika sukses)
            DB::table('carts')->where('user_id', $userId)->delete();

            DB::commit();

            return ['success' => true, 'order_id' => $orderId];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("CreateOrder Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create order. ' . $e->getMessage()];
        } finally {
            $lock->release();
        }
    }
}
