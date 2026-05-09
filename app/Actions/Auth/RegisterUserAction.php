<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterUserAction
{
    /**
     * Mengeksekusi pembuatan user baru dengan validasi dan enkripsi.
     *
     * @param array
     * @return User
     * @throws ValidationException
     */
    public function execute(array $data): User
    {
        $validator = Validator::make($data, [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Jika validasi gagal, lemparkan error
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Ambil data yang sudah dipastikan aman
        $validated = $validator->validated();

        return User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password'     => Hash::make($validated['password']),
            'role'         => 'Customer', 
        ]);
    }
}