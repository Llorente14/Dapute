<?php

namespace App\Actions\Checkout;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchBiteshipRatesAction
{
    public function execute(string $userId, string $destinationPostalCode, array $destinationAddress = [], string $courierType = 'regular'): array
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
                return ['success' => false, 'message' => 'Cart is empty.'];
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
                return ['success' => false, 'message' => 'Cart is empty or weight is invalid.'];
            }

            // Setup caching
            $originPostalCode = env('STORE_POSTAL_CODE', '11440');
            $destinationCoordinate = $this->normalizeCoordinate($destinationAddress['coordinates'] ?? null);
            $originCoordinate = $this->normalizeCoordinate([
                'latitude' => env('STORE_LATITUDE'),
                'longitude' => env('STORE_LONGITUDE'),
            ]);
            $couriers = $courierType === 'instant'
                ? env('BITESHIP_INSTANT_COURIERS', 'gosend,grab')
                : env('BITESHIP_POSTAL_COURIERS', 'jne,sicepat,jnt');

            if ($courierType === 'instant' && !$destinationCoordinate) {
                return ['success' => false, 'message' => 'Pick a map pin or use your current location for instant courier rates.'];
            }

            if ($courierType === 'instant' && !$originCoordinate) {
                Log::warning('Instant Biteship rates using dummy fallback: store origin coordinate is not configured.');

                return [
                    'success' => true,
                    'data' => $this->dummyRates($originPostalCode, $destinationPostalCode, (int) $totalWeight, $courierType),
                ];
            }

            $coordinateKey = $destinationCoordinate
                ? "{$destinationCoordinate['latitude']}_{$destinationCoordinate['longitude']}"
                : 'postal';
            $cacheKey = "biteship_rates_{$courierType}_{$originPostalCode}_{$destinationPostalCode}_{$coordinateKey}_{$totalWeight}_{$couriers}";

            $rates = Cache::remember($cacheKey, 300, function () use ($originPostalCode, $destinationPostalCode, $totalWeight, $items, $destinationCoordinate, $originCoordinate, $couriers, $courierType) {
                if (blank(env('BITESHIP_API_KEY'))) {
                    throw new \Exception('Biteship API key not configured.');
                }

                $payload = [
                    'couriers' => $couriers,
                    'items' => $items,
                ];

                if ($courierType === 'instant') {
                    $payload['origin_latitude'] = $originCoordinate['latitude'];
                    $payload['origin_longitude'] = $originCoordinate['longitude'];
                    $payload['destination_latitude'] = $destinationCoordinate['latitude'];
                    $payload['destination_longitude'] = $destinationCoordinate['longitude'];
                } else {
                    $payload['origin_postal_code'] = $originPostalCode;
                    $payload['destination_postal_code'] = $destinationPostalCode;
                }

                $response = Http::withToken(env('BITESHIP_API_KEY'))
                    ->timeout(10)
                    ->post('https://api.biteship.com/v1/rates/couriers', $payload);

                if ($response->json('success') === false) {
                    Log::warning('Biteship rates business error: ' . $response->body());

                    return $this->dummyRates($originPostalCode, $destinationPostalCode, (int) $totalWeight, $courierType);
                }

                if ($response->failed()) {
                    Log::error('Biteship API Error: ' . $response->body());
                    throw new \Exception('Failed to fetch data from shipping server.');
                }

                return $this->dummyRates($originPostalCode, $destinationPostalCode, (int) $totalWeight, $courierType);
            });

            if (empty($rates)) {
                return ['success' => false, 'message' => 'No shipping rates found for this address.'];
            }

            return [
                'success' => true,
                'data' => $rates,
            ];

        } catch (\Exception $e) {
            Log::warning('Biteship rates unavailable: ' . $e->getMessage());

            if (($courierType ?? 'regular') === 'instant' && isset($originPostalCode, $destinationPostalCode, $totalWeight)) {
                return [
                    'success' => true,
                    'data' => $this->dummyRates($originPostalCode, $destinationPostalCode, (int) $totalWeight, 'instant'),
                ];
            }

            return ['success' => false, 'message' => 'Failed to calculate shipping cost. Please try again later.'];
        }
    }

    private function dummyRates(string $originPostalCode, string $destinationPostalCode, int $totalWeight, string $courierType = 'regular'): array
    {
        $seed = abs(crc32("{$originPostalCode}|{$destinationPostalCode}|{$totalWeight}"));
        $basePrice = 5000 + ($seed % 25001);
        $standardPrice = (int) (ceil($basePrice / 1000) * 1000);
        $expressPrice = min(30000, $standardPrice + 7000);

        if ($courierType === 'instant') {
            return [
                [
                    'courier_code' => 'gosend',
                    'courier_name' => 'GoSend',
                    'courier_service_code' => 'instant',
                    'courier_service_name' => 'Instant Delivery',
                    'duration' => '1-2 hours',
                    'price' => $standardPrice,
                ],
                [
                    'courier_code' => 'grab',
                    'courier_name' => 'GrabExpress',
                    'courier_service_code' => 'instant',
                    'courier_service_name' => 'Instant Delivery',
                    'duration' => '1-2 hours',
                    'price' => $expressPrice,
                ],
            ];
        }

        return [
            [
                'courier_code' => 'local',
                'courier_name' => 'Local Courier',
                'courier_service_code' => 'standard',
                'courier_service_name' => 'Standard Delivery',
                'duration' => '2-4 hours',
                'price' => $standardPrice,
            ],
            [
                'courier_code' => 'local',
                'courier_name' => 'Local Courier',
                'courier_service_code' => 'express',
                'courier_service_name' => 'Express Delivery',
                'duration' => '1-2 hours',
                'price' => $expressPrice,
            ],
        ];
    }

    private function normalizeCoordinate(mixed $coordinates): ?array
    {
        if (is_string($coordinates)) {
            $decoded = json_decode($coordinates, true);
            $coordinates = is_array($decoded) ? $decoded : null;
        }

        if (!is_array($coordinates)) {
            return null;
        }

        $latitude = $coordinates['latitude'] ?? $coordinates['lat'] ?? null;
        $longitude = $coordinates['longitude'] ?? $coordinates['longtitude'] ?? $coordinates['lng'] ?? $coordinates['lon'] ?? null;

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return null;
        }

        return [
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
        ];
    }
}
