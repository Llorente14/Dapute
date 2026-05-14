<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterUserAction;
use Livewire\Attributes\Layout;

use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class RegisterForm extends Component
{
    // ─── Public properties bound to form fields ───────────────────────────────

    #[Validate('required|string|min:2|max:255', message: [
        'required' => 'Full name is required.',
        'min'      => 'Minimum 2 characters.',
        'max'      => 'Full name may not exceed 255 characters.',
    ])]
    public string $full_name = '';

    #[Validate('required|email:rfc|max:255', message: [
        'required' => 'Email address is required.',
        'email'    => 'Invalid email format.',
        'max'      => 'Email may not exceed 255 characters.',
    ])]
    public string $email = '';

    #[Validate(
        'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@!,.\?\/]).+$/',
        message: [
            'required' => 'Password is required.',
            'min'      => 'Minimum 8 characters.',
            'regex'    => 'Password is too weak. Use letters, numbers, and symbols (@!,.?/).',
        ]
    )]
    public string $password = '';

    #[Validate('required|string|same:password', message: [
        'required' => 'Please confirm your password.',
        'same'     => 'Passwords do not match.',
    ])]
    public string $password_confirmation = '';

    // Optional field – validated only for format when filled
    #[Validate('nullable|regex:/^[0-9+\-\s]{6,20}$/', message: [
        'regex' => 'Invalid phone number.',
    ])]
    public string $phone_number = '';

    // ─── State ────────────────────────────────────────────────────────────────

    /** Pesan error dari Action (misal: email duplikat, Supabase error) */
    public string $actionError = '';

    // ─── Reactive validation (dipicu saat field di-blur via wire:model.live) ──

    public function updatedFullName(): void
    {
        $this->validateOnly('full_name');
    }

    public function updatedEmail(): void
    {
        $this->validateOnly('email');
        // Validasi email unik TIDAK dilakukan di sini – serahkan ke RegisterUserAction
    }

    public function updatedPassword(): void
    {
        $this->validateOnly('password');

        // Re-validasi konfirmasi jika sudah terisi agar pesan error sinkron
        if ($this->password_confirmation !== '') {
            $this->validateOnly('password_confirmation');
        }
    }

    public function updatedPasswordConfirmation(): void
    {
        $this->validateOnly('password_confirmation');
    }

    public function updatedPhoneNumber(): void
    {
        $this->validateOnly('phone_number');
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    public function register(RegisterUserAction $action): void
    {
        // Reset error Action sebelumnya
        $this->actionError = '';

        // Full validation sebelum memanggil Action
        $this->validate();

        // Delegasikan ke Action – DILARANG panggil Supabase langsung dari sini
        $result = $action->execute([
            'full_name'    => $this->full_name,
            'email'        => $this->email,
            'password'     => $this->password,
            'phone_number' => $this->phone_number ?: null,
        ]);

        if (! $result['success']) {
            // Tangkap return array, tampilkan sebagai pesan – jangan throw exception ke UI
            $this->actionError = $result['message'] ?? 'Something went wrong. Please try again.';
            return;
        }

        // Berhasil → redirect ke /login dengan flash message sukses
        session()->flash('success', $result['message'] ?? 'Registration successful! Please sign in.');
        $this->redirect('/login', navigate: false);
    }

    // ─── View ─────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register-form');
    }
}
