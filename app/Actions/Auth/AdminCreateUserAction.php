<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AdminCreateUserAction
{
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|string|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'password'     => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        if (DB::table('users')->where('email', $data['email'])->exists()) {
            return ['success' => false, 'message' => 'Email ini sudah terdaftar.'];
        }

        $supabaseUrl = config('services.supabase.url');
        $serviceRoleKey = config('services.supabase.service_role_key');

        $authResponse = Http::withHeaders([
            'apikey'       => $serviceRoleKey,
            'Authorization' => "Bearer {$serviceRoleKey}",
            'Content-Type' => 'application/json',
        ])->post("{$supabaseUrl}/auth/v1/admin/users", [
            'email'    => $data['email'],
            'password' => $data['password'],
            'email_confirm' => true,
        ]);

        if (!$authResponse->successful()) {
            $errorMsg = $authResponse->json('msg') ?? $authResponse->json('message') ?? 'Terjadi kesalahan pada server otentikasi.';
            return ['success' => false, 'message' => 'Gagal membuat user auth: ' . $errorMsg];
        }

        $authData = $authResponse->json();
        $uid = $authData['id'] ?? null;

        if (!$uid) {
            return ['success' => false, 'message' => 'Gagal mendapatkan data User ID.'];
        }

        try {
            DB::table('users')->insert([
                'id'           => $uid,
                'full_name'    => $data['full_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'email'        => $data['email'],
                'role'         => 'staff',
                'is_active'    => DB::raw('true'),
                'created_at'   => now(),
            ]);

            return ['success' => true, 'message' => 'Akun berhasil ditambahkan.'];
        } catch (\Exception $e) {
            Log::error('AdminCreateUser Error: ' . $e->getMessage());
            Http::withHeaders([
                'apikey'       => $serviceRoleKey,
                'Authorization' => "Bearer {$serviceRoleKey}",
            ])->delete("{$supabaseUrl}/auth/v1/admin/users/{$uid}");

            return ['success' => false, 'message' => 'Gagal menyimpan profil: ' . $e->getMessage()];
        }
    }
}
