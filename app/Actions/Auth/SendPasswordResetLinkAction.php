<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPasswordResetLinkAction
{
    /**
     * Mengeksekusi pengiriman link reset password via Supabase Auth.
     */
    public function execute(string $email): array
    {
        $supabaseUrl = config('services.supabase.url');
        $supabaseKey = config('services.supabase.anon_key');
        $resetUrl = route('password.reset');

        $response = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Content-Type' => 'application/json',
        ])->post("{$supabaseUrl}/auth/v1/recover?redirect_to={$resetUrl}", [
            'email' => $email,
        ]);

        if (!$response->successful()) {
            $errorData = $response->json();
            $isRateLimited = isset($errorData['error_code']) && $errorData['error_code'] === 'over_email_send_rate_limit';
            
            Log::error('Supabase send reset link failed', [
                'status' => $response->status(),
                'error' => $errorData,
            ]);

            return [
                'success' => false,
                'message' => $isRateLimited 
                    ? 'Email rate limit exceeded. Please wait a while before requesting another password reset.'
                    : 'Failed to send password reset link. Please try again later.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Password reset link sent successfully.',
        ];
    }
}
