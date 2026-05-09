<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LogoutUserAction
{
    /**
     * Menangani proses keluar dari Supabase Auth dan membersihkan session Laravel.
     */
    public function execute(): array
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_ANON_KEY');
        $token = Session::get('supabase_token');

        if ($token) {
            try {
                Http::withHeaders([
                    'apikey' => $supabaseKey,
                    'Authorization' => "Bearer {$token}",
                ])->post("{$supabaseUrl}/auth/v1/logout");
            } catch (\Exception $e) {
                //
            }
        }

        Session::forget('supabase_token');
        Session::forget('user_id');
        

        return [
            'success' => true, 
            'message' => 'Berhasil keluar.'
        ];
    }
}