<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel kosong sebelum diisi (opsional)
        // DB::table('users')->truncate(); 

        $users = [
            [
                'id' => (string) Str::uuid(), // UUID dummy
                'full_name' => 'Admin Dapute',
                'phone_number' => '08123456789',
                'email' => 'admin@dapute.com',
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'full_name' => 'Customer Aktif',
                'phone_number' => '08129998887',
                'email' => 'customer@gmail.com',
                'role' => 'customer',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'full_name' => 'User Nonaktif',
                'phone_number' => '08120000000',
                'email' => 'suspend@gmail.com',
                'role' => 'customer',
                'is_active' => false, // Untuk tes LoginUserAction (SCRUM-29)
                'created_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}