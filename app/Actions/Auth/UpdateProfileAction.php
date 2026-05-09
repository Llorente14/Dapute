<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\DB;

class UpdateProfileAction
{
    /**
     * Update full_name dan phone_number di tabel users.
     */
    public function execute(string $userId, array $data): array
    {
        $updateData = array_intersect_key($data, array_flip(['full_name', 'phone_number']));

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data valid yang dikirim untuk diperbarui.',
            ];
        }

        try {
            DB::table('users')
                ->where('id', $userId)
                ->update($updateData);

            return [
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage(),
            ];
        }
    }
}