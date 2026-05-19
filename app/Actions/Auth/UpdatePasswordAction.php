<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdatePasswordAction
{
    /**
     * Mengeksekusi update password via Supabase Auth.
     */
    public function execute(string $accessToken, string $newPassword): array
    {
        $supabaseUrl = config('services.supabase.url');
        $supabaseKey = config('services.supabase.anon_key');

        $response = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json',
        ])->put("{$supabaseUrl}/auth/v1/user", [
            'password' => $newPassword,
        ]);

        if (!$response->successful()) {
            // Log error tapi jangan bocorkan token
            Log::error('Supabase password reset failed', [
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update password. Your reset link may be invalid or expired.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Password updated successfully.',
        ];
    }
}
