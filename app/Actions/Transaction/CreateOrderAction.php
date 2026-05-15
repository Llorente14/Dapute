<?php

namespace App\Actions\Transaction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateOrderAction
{
    public function execute(string $userId, int $shippingFee, string $notes = null): array
    {
        // Cegah double click dengan Cache Lock (10 detik)
        $lock = Cache::lock("create_order_lock_{$userId}", 10);

        if (!$lock->get()) {
            return ['success' => false, 'message' => 'Pesanan sedang diproses, mohon tunggu.'];
        }

        DB::beginTransaction();
        try {
            // Ambil data keranjang beserta snapshot harga
            $cartItems = DB::table('carts')
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Keranjang belanja kosong.');
            }

            // Hitung subtotal
            $subtotalAmount = 0;
            foreach ($cartItems as $item) {
                $subtotalAmount += ($item->price_snapshot * $item->quantity);
            }

            $totalPayment = $subtotalAmount + $shippingFee;
            $orderId = (string) Str::uuid();

            // Insert ke tabel orders
            DB::table('orders')->insert([
                'id' => $orderId,
                'customer_id' => $userId,
                'subtotal_amount' => $subtotalAmount,
                'shipping_fee' => $shippingFee,
                'total_payment' => $totalPayment,
                'notes' => $notes,
                'order_status' => 'PENDING_PAYMENT',
                'created_at' => now(),
                'order_date' => now(),
            ]);

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
            return ['success' => false, 'message' => 'Gagal membuat pesanan. ' . $e->getMessage()];
        } finally {
            $lock->release();
        }
    }
}