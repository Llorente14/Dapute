<?php

namespace App\Actions\Logistics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class RequestBiteshipPickupAction
{
    protected UpdateOrderStatusAction $updateStatusAction;

    public function __construct(UpdateOrderStatusAction $updateStatusAction)
    {
        $this->updateStatusAction = $updateStatusAction;
    }

    public function execute(string $orderId, string $courierCompany = 'gojek', string $courierType = 'instant'): array
    {
        $order = DB::table('orders')->where('id', $orderId)->first();
        if (!$order) {
            return ['success' => false, 'message' => 'Pesanan tidak ditemukan.'];
        }

        if ($order->order_status !== 'PICKUP_REQUESTED') {
            return ['success' => false, 'message' => 'Hanya pesanan yang sudah selesai diproses yang bisa dipanggilkan kurir.'];
        }

        if (!empty($order->biteship_order_id)) {
            return ['success' => false, 'message' => 'Kurir sudah di-request untuk pesanan ini.'];
        }

        try {
            $address = DB::table('order_addresses')->where('order_id', $orderId)->first();
            if (!$address) {
                throw new Exception('Alamat pengiriman pelanggan tidak ditemukan.');
            }

            $isInstant = in_array(strtolower($courierCompany), ['gojek', 'grab', 'borzo', 'lalamove']);
            
            if ($isInstant && (empty($address->latitude) || empty($address->longitude))) {
                return [
                    'success' => false, 
                    'message' => 'Gagal memanggil kurir instan. Pelanggan wajib menandai (pin) lokasi peta terlebih dahulu.'
                ];
            }

            $items = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('order_items.order_id', $orderId)
                ->select('order_items.*', 'products.weight_gram')
                ->get();

            $biteshipItems = [];
            foreach ($items as $item) {
                $biteshipItems[] = [
                    'name'     => $item->cake_name_snapshot,
                    'value'    => (int) $item->price_snapshot,
                    'quantity' => (int) $item->quantity,
                    'weight'   => (int) ($item->weight_gram ?? 500)
                ];
            }

            $payload = [
                'origin_contact_name'       => env('STORE_NAME', 'DapuTe Bakery'),
                'origin_contact_phone'      => env('STORE_PHONE', '081234567890'),
                'origin_address'            => env('STORE_ADDRESS', 'Jl. Letjen S. Parman No.1, Jakarta Barat'),
                'origin_postal_code'        => env('STORE_POSTAL_CODE', '11440'),
                'origin_coordinate' => [
                    'latitude'  => (float) env('STORE_LAT', -6.1674),
                    'longitude' => (float) env('STORE_LNG', 106.7823),
                ],
                'destination_contact_name'  => $address->recipient_name,
                'destination_contact_phone' => $address->recipient_phone,
                'destination_address'       => $address->shipping_address,
                'destination_postal_code'   => $address->postal_code,
                
                'courier_company'           => strtolower($courierCompany), 
                'courier_type'              => strtolower($courierType),
                'delivery_type'             => 'now',
                'items'                     => $biteshipItems
            ];

            // Masukkan koordinat tujuan HANYA JIKA datanya ada di database
            if (!empty($address->latitude) && !empty($address->longitude)) {
                $payload['destination_coordinate'] = [
                    'latitude'  => (float) $address->latitude,
                    'longitude' => (float) $address->longitude,
                ];
            }

            $response = Http::withToken(env('BITESHIP_API_KEY'))
                ->timeout(15) 
                ->post('https://api.biteship.com/v1/orders', $payload);

            if ($response->failed()) {
                Log::error('Biteship Create Order Error: ' . $response->body());
                return ['success' => false, 'message' => 'Gagal memanggil kurir dari Biteship. Coba lagi nanti.'];
            }

            $responseData = $response->json();
            $biteshipOrderId = $responseData['id'] ?? null;
            $trackingId = $responseData['courier']['waybill_id'] ?? 'TRACKING-SANDBOX-' . rand(1000, 9999);

            DB::beginTransaction();

            DB::table('orders')->where('id', $orderId)->update([
                'biteship_order_id' => $biteshipOrderId,
                'tracking_id'       => $trackingId
            ]);

            $this->updateStatusAction->execute($orderId, UpdateOrderStatusAction::STATUS_SHIPPED);

            DB::commit();

            return [
                'success'           => true,
                'message'           => 'Kurir berhasil dipanggil!',
                'courier'           => $courierCompany,
                'tracking_id'       => $trackingId
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('RequestBiteshipPickupAction Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }
}