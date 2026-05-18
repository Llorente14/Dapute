<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminDeleteUserAction
{
    public function execute(string $userId): array
    {
        try {
            $supabaseUrl = config('services.supabase.url');
            $serviceRoleKey = config('services.supabase.service_role_key');

            $authResponse = Http::withHeaders([
                'apikey'       => $serviceRoleKey,
                'Authorization' => "Bearer {$serviceRoleKey}",
            ])->delete("{$supabaseUrl}/auth/v1/admin/users/{$userId}");

            if (!$authResponse->successful() && $authResponse->status() !== 404) {
                $errorMsg = $authResponse->json('msg') ?? $authResponse->json('message') ?? 'Gagal menghapus user di server otentikasi.';
                return ['success' => false, 'message' => $errorMsg];
            }

            DB::table('users')->where('id', $userId)->delete();

            return ['success' => true, 'message' => 'Pengguna berhasil dihapus permanen.'];
        } catch (\Exception $e) {
            Log::error('AdminDeleteUser Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menghapus pengguna: ' . $e->getMessage()];
        }
    }
}
