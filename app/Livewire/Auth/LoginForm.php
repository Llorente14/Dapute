<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class LoginForm extends Component
{
    // ─── Public properties bound to form fields ───────────────────────────────

    #[Validate('required|email', message: [
        'required' => 'Email address is required.',
        'email'    => 'Invalid email format.',
    ])]
    public string $email = '';

    #[Validate('required', message: [
        'required' => 'Password is required.',
    ])]
    public string $password = '';

    /** Pesan error dari Action (misal: password salah, akun nonaktif) */
    public string $actionError = '';

    // ─── Submit ───────────────────────────────────────────────────────────────

    /**
     * Login handler — SCRUM-28 (Rendy) akan meng-wire method ini
     * ke LoginUserAction untuk autentikasi via Supabase Auth.
     *
     * Flow yang diharapkan:
     * 1. $this->validate()
     * 2. $result = $action->execute($this->email, $this->password)
     * 3. Jika gagal: $this->actionError = $result['message']
     * 4. Jika sukses: redirect ke /catalog
     */
    public function login(): void
    {
        $this->actionError = '';
        $this->validate();

        // TODO: SCRUM-28 — Wire ke LoginUserAction
        // $result = $action->execute($this->email, $this->password);
        // if (! $result['success']) {
        //     $this->actionError = $result['message'];
        //     return;
        // }
        // session()->flash('success', 'Login berhasil!');
        // $this->redirect('/catalog', navigate: false);
    }

    // ─── View ─────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
