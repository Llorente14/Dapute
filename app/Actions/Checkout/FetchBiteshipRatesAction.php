<?php

namespace App\Actions\Checkout;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchBiteshipRatesAction
{
    public function execute(string $userId, string $destinationPostalCode): array
    {
        try {
            // Hitung total berat dari tabel carts join products
            $totalWeight = DB::table('carts')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->where('carts.user_id', $userId)
                ->sum(DB::raw('carts.quantity * products.weight_gram'));

            if ($totalWeight <= 0) {
                return ['success' => false, 'message' => 'Keranjang kosong atau berat tidak valid.'];
            }

            // Setup caching
            $originPostalCode = env('STORE_POSTAL_CODE', '11440');
            $cacheKey = "biteship_rates_{$originPostalCode}_{$destinationPostalCode}_{$totalWeight}";

            $rates = Cache::remember($cacheKey, 300, function () use ($originPostalCode, $destinationPostalCode, $totalWeight) {
                $response = Http::withToken(env('BITESHIP_API_KEY'))
                    ->timeout(10)
                    ->post('https://api.biteship.com/v1/rates/couriers', [
                        'origin_postal_code' => $originPostalCode,
                        'destination_postal_code' => $destinationPostalCode,
                        'couriers' => 'gosend,grab',
                        'items' => [
                            [
                                'name' => 'Kue Kering DapuTe',
                                'value' => 100000,
                                'weight' => $totalWeight,
                                'quantity' => 1
                            ]
                        ]
                    ]);

                if ($response->failed()) {
                    Log::error('Biteship API Error: ' . $response->body());
                    throw new \Exception('Gagal mengambil data dari server kurir.');
                }

                return $response->json('pricing');
            });

            return ['success' => true, 'data' => $rates];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Layanan pengecekan ongkir sedang gangguan atau alamat di luar jangkauan (Timeout).'];
        }
    }
}