<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RegisterUserAction
{
    /**
     * Mengeksekusi pendaftaran via Supabase Auth dan insert ke DB.
     *
     * @param array
     * @return array
     */
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email'        => 'required|string|email|max:255',
            'password'     => 'required|string|min:8',
        ], [
            'email.email'  => 'Format email tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
                'user'    => null,
            ];
        }

        $emailExists = DB::table('users')->where('email', $data['email'])->exists();
        if ($emailExists) {
            return [
                'success' => false,
                'message' => 'Email ini sudah terdaftar. Silakan gunakan email lain.',
                'user'    => null,
            ];
        }

        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_ANON_KEY');

        $authResponse = Http::withHeaders([
            'apikey'       => $supabaseKey,
            'Authorization'=> "Bearer {$supabaseKey}",
            'Content-Type' => 'application/json',
        ])->post("{$supabaseUrl}/auth/v1/signup", [
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$authResponse->successful()) {
            $errorMsg = $authResponse->json('msg') ?? 'Terjadi kesalahan pada server otentikasi.';
            return [
                'success' => false,
                'message' => $errorMsg,
                'user'    => null,
            ];
        }

        $authData = $authResponse->json();
        $uid = $authData['user']['id'] ?? ($authData['id'] ?? null);

        if (!$uid) {
            return [
                'success' => false,
                'message' => 'Gagal mendapatkan data User ID dari server otentikasi.',
                'user'    => null,
            ];
        }

        try {
            $newUser = [
                'id'           => $uid,
                'full_name'    => $data['full_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'email'        => $data['email'],
                'role'         => 'customer',
                'is_active'    => true,
                'created_at'   => now(),
            ];

            DB::table('users')->insert($newUser);

            return [
                'success' => true,
                'message' => 'Pendaftaran berhasil.',
                'user'    => $newUser,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menyimpan profil pengguna: ' . $e->getMessage(),
                'user'    => null,
            ];
        }
    }
}