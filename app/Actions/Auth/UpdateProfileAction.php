<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Http;

class UpdateProfileAction
{
    /**
     * Update full_name dan phone_number di tabel users.
     */
    public function execute(string $userId, array $data): array
    {
        $updateData = array_intersect_key($data, array_flip(['full_name', 'phone_number']));

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data valid yang dikirim untuk diperbarui.',
            ];
        }

        try {
            $supabaseUrl = config('services.supabase.url');
            $serviceRoleKey = config('services.supabase.service_role_key');

            $response = Http::withHeaders([
                'apikey' => $serviceRoleKey,
                'Authorization' => "Bearer {$serviceRoleKey}",
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal'
            ])->patch("{$supabaseUrl}/rest/v1/users?id=eq.{$userId}", $updateData);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Gagal memperbarui profil: ' . $response->body(),
                ];
            }

            return [
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage(),
            ];
        }
    }
}