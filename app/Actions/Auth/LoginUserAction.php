<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginUserAction
{
    /**
     * Mengeksekusi login via Supabase Auth dan pengecekan status aktif.
     */
    public function execute(string $email, string $password): array
    {
        $supabaseUrl = config('services.supabase.url');
        $supabaseKey = config('services.supabase.anon_key');

        $response = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Content-Type' => 'application/json',
        ])->post("{$supabaseUrl}/auth/v1/token?grant_type=password", [
            'email' => $email,
            'password' => $password,
        ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'Email atau password salah',
            ];
        }

        $authData = $response->json();
        $uid = $authData['user']['id'];
        $accessToken = $authData['access_token'];
        $userProfile = DB::table('users')->where('id', $uid)->first();

        if (!$userProfile) {
            return [
                'success' => false,
                'message' => 'Profil pengguna tidak ditemukan.',
            ];
        }

        if (!$userProfile->is_active) {
            return [
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan',
            ];
        }

        Session::put('supabase_token', $accessToken);
        Session::put('user_id', $uid);

        return [
            'success' => true,
            'token' => $accessToken,
            'user' => [
                'id' => $userProfile->id,
                'full_name' => $userProfile->full_name,
                'email' => $userProfile->email,
                'role' => $userProfile->role,
                'is_active' => $userProfile->is_active,
            ]
        ];
    }
}
