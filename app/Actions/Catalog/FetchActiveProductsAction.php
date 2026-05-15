<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchActiveProductsAction
{
    /**
     * Mengambil daftar produk yang berstatus aktif.
     * Mendukung pencarian berdasarkan nama kue secara case-insensitive.
     *
     * @param string|null
     * @return array
     */
    public function execute(?string $search = null, ?string $sort = 'Terbaru'): array
    {
        try {
            $query = DB::table('products')
                ->select([
                    'id',
                    'cake_name',
                    'description',
                    'price',
                    'weight_grams',
                    'image_url',
                    'is_active',
                    'created_at'
                ])
                ->whereRaw('is_active = true');

            if (!empty($search)) {
                $query->where('cake_name', 'ILIKE', '%' . $search . '%');
            }

            switch ($sort) {
                case 'Harga Terendah':
                    $query->orderBy('price', 'asc');
                    break;
                case 'Harga Tertinggi':
                    $query->orderBy('price', 'desc');
                    break;
                case 'Nama A-Z':
                    $query->orderBy('cake_name', 'asc');
                    break;
                case 'new':
                    $query->orderBy('created_at', 'desc')->limit(4);
                    break;
                case 'all':
                    $query->orderBy('created_at', 'desc')->limit(8);
                    break;
                case 'Terbaru':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            return $query->get()->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'cake_name'    => $item->cake_name,
                    'description'  => $item->description,
                    'price'        => 'Rp ' . number_format($item->price, 0, ',', '.'),
                    'weight_grams' => (int) $item->weight_grams,
                    'image_url'    => $item->image_url,
                    'is_active'    => (bool) $item->is_active,
                    'created_at'   => $item->created_at,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error("Gagal fetch produk: " . $e->getMessage());
            return [];
        }
    }
}
