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
    public function execute(?string $search = null): array
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
                ->where('is_active', true);

            if (!empty($search)) {
                $query->where('cake_name', 'ILIKE', '%' . $search . '%');
            }

            return $query->get()->map(function ($item) {
                return (array) $item;
            })->toArray();

        } catch (\Exception $e) {
            Log::error("Gagal fetch produk: " . $e->getMessage());
            return [];
        }
    }
}