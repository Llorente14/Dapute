<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

class AddressManager
{
    /**
     * Memvalidasi struktur data alamat dari localStorage.
     */
    public static function validateAddress(array $data): array
    {
        $validator = Validator::make($data, [
            'label'           => 'required|string|max:50',
            'recipient_name'  => 'required|string|max:255',
            'recipient_phone' => ['required', 'regex:/^[0-9+\-\s]{6,20}$/'],
            'address'         => 'required|string',
            'city'            => 'required|string',
            'postal_code'     => 'required|digits_between:4,10',
        ]);

        if ($validator->fails()) {
            return [
                'valid'  => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return [
            'valid'  => true,
            'errors' => []
        ];
    }
}