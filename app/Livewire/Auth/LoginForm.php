<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class LoginForm extends Component
{
    #[Validate('required|email', message: [
        'required' => 'Email address is required.',
        'email' => 'Invalid email address.',
    ])]
    public string $email = '';

    #[Validate('required', message: [
        'required' => 'Password is required.',
    ])]
    public string $password = '';


    public function login()
    {
        $this->validate();

        // Check if user exists first to give specific email error as requested
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            $this->addError('email', 'Invalid email address.');
            return;
        }

        // Attempt login
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->addError('password', 'Invalid password.');
            return;
        }

        // Redirect on success
        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
