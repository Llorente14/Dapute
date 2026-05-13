<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class StoreProductAction
{
    /**
     * Menyimpan produk baru ke database dan mengunggah gambar ke Supabase Storage.
     * 
     * @param array
     * @param UploadedFile|null 
     * @return array
     */
    public function execute(array $data, ?UploadedFile $image = null): array
    {
        try {
            // 1. Validasi server-side sederhana
            if (empty($data['cake_name']) || (int)$data['price'] <= 0 || (int)$data['weight_grams'] <= 0) {
                return [
                    'success' => false, 
                    'message' => 'Validasi gagal: Nama wajib diisi, harga & berat harus positif.'
                ];
            }

            $supabaseUrl = env('SUPABASE_URL');
            $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');
            $imageUrl = null;
            
            // Generate UUID di awal untuk sinkronisasi path folder storage dan ID database
            $newId = Str::uuid();

            // 2. Logic Upload Gambar ke Supabase Storage (Jika ada)
            if ($image) {
                // Sanitize nama file: hapus spasi & karakter spesial
                $extension = $image->getClientOriginalExtension();
                $safeFilename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;
                
                // Struktur path: products/{uuid_produk}/{filename}
                $path = "products/{$newId}/{$safeFilename}";

                $upload = Http::withHeaders([
                    'Authorization' => "Bearer {$serviceKey}",
                    'Content-Type' => $image->getMimeType(),
                ])->withBody(file_get_contents($image->getRealPath()), $image->getMimeType())
                  ->post("{$supabaseUrl}/storage/v1/object/product-images/{$path}");

                if (!$upload->successful()) {
                    return ['success' => false, 'message' => 'Gagal mengunggah gambar ke Storage.'];
                }

                $imageUrl = "{$supabaseUrl}/storage/v1/object/public/product-images/{$path}";
            }

            // 3. INSERT row baru ke tabel 'products'
            DB::table('products')->insert([
                'id'           => $newId,
                'cake_name'    => $data['cake_name'],
                'description'  => $data['description'] ?? null,
                'price'        => (int) $data['price'],
                'weight_grams' => (int) $data['weight_grams'],
                'image_url'    => $imageUrl,
                'is_active'    => $data['is_active'] ?? true,
                'created_at'   => now(),
            ]);

            return [
                'success' => true, 
                'product_id' => (string) $newId
            ];

        } catch (\Exception $e) {
            // Log error untuk kebutuhan debugging internal
            Log::error("StoreProductAction Error: " . $e->getMessage());
            
            // Return array kosong/false tanpa throw exception ke caller
            return [
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem saat menyimpan produk.'
            ];
        }
    }
}