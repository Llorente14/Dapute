<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateUserRoleAction
{
    /**
     * Memperbarui Role dan Status Aktif Pengguna
     */
    public function update(string $userId, string $role, bool $status): array
    {
        try {
            // Validasi Strict Enum sesuai constraint tabel Supabase
            $allowedRoles = ['customer', 'admin', 'owner', 'staff'];
            if (!in_array($role, $allowedRoles)) {
                return ['success' => false, 'message' => 'Role tidak valid. Harus berupa owner, admin, staff, atau customer.'];
            }

            DB::table('users')->where('id', $userId)->update([
                'role' => $role,
                'is_active' => DB::raw($status ? 'true' : 'false')
            ]);

            return ['success' => true, 'message' => 'Data pengguna berhasil diperbarui.'];
        } catch (\Exception $e) {
            Log::error("UpdateUserRole Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memperbarui data pengguna.'];
        }
    }

    /**
     * Mengirim token reset password menggunakan Endpoint Supabase Auth
     */
    public function sendPasswordReset(string $email): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('SUPABASE_URL') . '/auth/v1/recover', [
                'email' => $email,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Instruksi reset password berhasil dikirim ke email target.'];
            }

            return ['success' => false, 'message' => 'Gagal mengirim instruksi reset password. Pastikan email terdaftar.'];
        } catch (\Exception $e) {
            Log::error("ResetPassword Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem saat menghubungi Supabase.'];
        }
    }
}