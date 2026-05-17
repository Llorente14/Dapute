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
            $allowedRoles = ['customer', 'admin', 'owner', 'staff'];
            if (!in_array($role, $allowedRoles)) {
                return ['success' => false, 'message' => 'Invalid role. Must be owner, admin, staff, or customer.'];
            }

            DB::table('users')->where('id', $userId)->update([
                'role' => $role,
                'is_active' => DB::raw($status ? 'true' : 'false')
            ]);

            return ['success' => true, 'message' => 'User data successfully updated.'];
        } catch (\Exception $e) {
            Log::error("UpdateUserRole Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update user data.'];
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
                return ['success' => true, 'message' => 'Password reset instructions successfully sent to target email.'];
            }

            return ['success' => false, 'message' => 'Failed to send password reset instructions. Make sure the email is registered.'];
        } catch (\Exception $e) {
            Log::error("ResetPassword Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'System error occurred while contacting Supabase.'];
        }
    }
}