<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminUpdateUserAction
{
    public function execute(string $userId, array $data): array
    {
        try {
            if (isset($data['role']) && !in_array($data['role'], ['owner', 'admin', 'customer'], true)) {
                return ['success' => false, 'message' => 'Invalid role. Must be owner, Admin, or customer.'];
            }

            $supabaseUrl = config('services.supabase.url');
            $serviceRoleKey = config('services.supabase.service_role_key');

            if (isset($data['email'])) {
                $authResponse = Http::withHeaders([
                    'apikey'       => $serviceRoleKey,
                    'Authorization' => "Bearer {$serviceRoleKey}",
                    'Content-Type' => 'application/json',
                ])->put("{$supabaseUrl}/auth/v1/admin/users/{$userId}", [
                    'email' => $data['email'],
                    'email_confirm' => true,
                ]);

                if (!$authResponse->successful()) {
                    $errorMsg = $authResponse->json('msg') ?? $authResponse->json('message') ?? 'Gagal memperbarui email otentikasi.';
                    return ['success' => false, 'message' => $errorMsg];
                }
            }

            $updateData = [];
            if (isset($data['full_name'])) $updateData['full_name'] = $data['full_name'];
            if (isset($data['email'])) $updateData['email'] = $data['email'];
            if (isset($data['phone_number'])) $updateData['phone_number'] = $data['phone_number'];
            if (isset($data['role'])) $updateData['role'] = $data['role'];
            if (isset($data['is_active'])) $updateData['is_active'] = DB::raw($data['is_active'] ? 'true' : 'false');

            if (!empty($updateData)) {
                DB::table('users')->where('id', $userId)->update($updateData);
            }

            return ['success' => true, 'message' => 'Data pengguna berhasil diperbarui.'];
        } catch (\Exception $e) {
            Log::error('AdminUpdateUser Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memperbarui pengguna: ' . $e->getMessage()];
        }
    }
}
