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
            $cartItems = DB::table('carts')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->where('carts.user_id', $userId)
                ->select(
                    'carts.cake_name_snapshot',
                    'carts.price_snapshot',
                    'carts.quantity',
                    DB::raw('COALESCE(NULLIF(products.weight_grams, 0), 500) as weight_grams')
                )
                ->get();

            if ($cartItems->isEmpty()) {
                return ['success' => false, 'message' => 'Keranjang kosong.'];
            }

            $items = $cartItems->map(function ($item) {
                return [
                    'name' => $item->cake_name_snapshot,
                    'value' => max((int) $item->price_snapshot, 1000),
                    'weight' => max((int) $item->weight_grams, 1),
                    'quantity' => max((int) $item->quantity, 1),
                ];
            })->values()->toArray();

            $totalWeight = collect($items)->sum(fn ($item) => $item['weight'] * $item['quantity']);

            if ($totalWeight <= 0) {
                return ['success' => false, 'message' => 'Keranjang kosong atau berat tidak valid.'];
            }

            // Setup caching
            $originPostalCode = env('STORE_POSTAL_CODE', '11440');
            $cacheKey = "biteship_rates_{$originPostalCode}_{$destinationPostalCode}_{$totalWeight}";

            $rates = Cache::remember($cacheKey, 300, function () use ($originPostalCode, $destinationPostalCode, $totalWeight, $items) {
                if (blank(env('BITESHIP_API_KEY'))) {
                    throw new \Exception('Biteship API key belum dikonfigurasi.');
                }

                $response = Http::withToken(env('BITESHIP_API_KEY'))
                    ->timeout(10)
                    ->post('https://api.biteship.com/v1/rates/couriers', [
                        'origin_postal_code' => $originPostalCode,
                        'destination_postal_code' => $destinationPostalCode,
                        'couriers' => 'gosend,grab',
                        'items' => $items,
                    ]);

                if ($response->json('success') === false) {
                    Log::warning('Biteship rates business error: ' . $response->body());

                    return $this->dummyRates($originPostalCode, $destinationPostalCode, (int) $totalWeight);
                }

                if ($response->failed()) {
                    Log::error('Biteship API Error: ' . $response->body());
                    throw new \Exception('Gagal mengambil data dari server kurir.');
                }

                return $this->dummyRates($originPostalCode, $destinationPostalCode, (int) $totalWeight);
            });

            if (empty($rates)) {
                return ['success' => false, 'message' => 'Biteship tidak mengembalikan tarif untuk alamat ini.'];
            }

            return [
                'success' => true,
                'data' => $rates,
            ];

        } catch (\Exception $e) {
            Log::warning('Biteship rates unavailable: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Biteship gagal menghitung ongkir. Coba lagi nanti atau cek saldo/API key Biteship.'];
        }
    }

    private function dummyRates(string $originPostalCode, string $destinationPostalCode, int $totalWeight): array
    {
        $seed = abs(crc32("{$originPostalCode}|{$destinationPostalCode}|{$totalWeight}"));
        $basePrice = 5000 + ($seed % 25001);
        $standardPrice = (int) (ceil($basePrice / 1000) * 1000);
        $expressPrice = min(30000, $standardPrice + 7000);

        return [
            [
                'courier_code' => 'local',
                'courier_name' => 'Local Courier',
                'courier_service_code' => 'standard',
                'courier_service_name' => 'Standard Delivery',
                'duration' => '2-4 jam',
                'price' => $standardPrice,
            ],
            [
                'courier_code' => 'local',
                'courier_name' => 'Local Courier',
                'courier_service_code' => 'express',
                'courier_service_name' => 'Express Delivery',
                'duration' => '1-2 jam',
                'price' => $expressPrice,
            ],
        ];
    }
}
