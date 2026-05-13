<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteProductAction
{
    public function execute(string $id): array
    {
        try {
            $product = DB::table('products')->where('id', $id)->first();
            
            if (!$product) {
                return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
            }

            // 1. Soft Delete
            DB::table('products')->where('id', $id)->update([
                'is_active' => false,
                'updated_at' => now() 
            ]);

            // 2. Hapus Gambar dari Storage
            if ($product->image_url) {
                $supabaseUrl = env('SUPABASE_URL');
                $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');

                $path = str_replace($supabaseUrl . "/storage/v1/object/public/product-images/", "", $product->image_url);
                
                $response = Http::withHeaders(['Authorization' => "Bearer " . $serviceKey])
                    ->delete($supabaseUrl . "/storage/v1/object/product-images/{$path}");

                if (!$response->successful()) {
                    Log::warning("Gagal menghapus file storage untuk produk ID: {$id}");
                }
            }

            return ['success' => true, 'message' => 'Produk berhasil dinonaktifkan.'];

        } catch (\Exception $e) {
            Log::error("DeleteProductAction Error: " . $e->getMessage());
            
            return ['success' => false, 'message' => 'Gagal memproses penghapusan produk.'];
        }
    }
}