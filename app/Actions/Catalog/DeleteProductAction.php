<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DeleteProductAction
{
    public function execute(string $id): array
    {
        try {
            $product = DB::table('products')->where('id', $id)->first();
            if (!$product) return ['success' => false, 'message' => 'Produk tidak ditemukan.'];

            // 1. Soft Delete (is_active = false)
            DB::table('products')->where('id', $id)->update(['is_active' => false]);

            // 2. Hapus Gambar dari Storage (Jika ada)
            if ($product->image_url) {
                $path = str_replace(env('SUPABASE_URL') . "/storage/v1/object/public/product-images/", "", $product->image_url);
                Http::withHeaders(['Authorization' => "Bearer " . env('SUPABASE_SERVICE_ROLE_KEY')])
                    ->delete(env('SUPABASE_URL') . "/storage/v1/object/product-images/{$path}");
            }

            return ['success' => true, 'message' => 'Produk berhasil dinonaktifkan.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal menghapus produk.'];
        }
    }
}