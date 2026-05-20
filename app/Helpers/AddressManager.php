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
        $data = self::normalize($data);

        $validator = Validator::make($data, [
            'label'           => 'required|string|max:50',
            'recipient_name'  => 'required|string|min:2|max:255',
            'recipient_phone' => ['required', 'regex:/^\+62\d{8,11}$/'],
            'address'         => 'required|string|min:5',
            'city'            => 'required|string|min:2',
            'postal_code'     => 'required|digits:5',
        ], [
            'recipient_phone.regex' => 'Phone number must start with +62 followed by 8 to 11 digits.',
            'postal_code.digits' => 'Postal code must contain exactly 5 digits.',
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

    public static function normalizePhoneNumber(string $phoneNumber): string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber) ?? '';

        if (str_starts_with($digits, '62')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return '+62'.substr($digits, 0, 11);
    }

    public static function localPhoneNumber(string $phoneNumber): string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber) ?? '';

        if (str_starts_with($digits, '62')) {
            return substr($digits, 2, 11);
        }

        if (str_starts_with($digits, '0')) {
            return substr($digits, 1, 11);
        }

        return substr($digits, 0, 11);
    }

    private static function normalize(array $data): array
    {
        foreach (['label', 'recipient_name', 'recipient_phone', 'address', 'city', 'postal_code'] as $field) {
            $data[$field] = trim((string) ($data[$field] ?? ''));
        }

        $data['recipient_phone'] = self::normalizePhoneNumber($data['recipient_phone']);

        return $data;
    }
}
