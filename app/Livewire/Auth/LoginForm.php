<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Actions\Auth\LoginUserAction;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.guest')]
class LoginForm extends Component
{
    #[Validate('required', message: 'Email address is required.')]
    #[Validate('email', message: 'Invalid email address.')]
    public string $email = '';

    #[Validate('required', message: 'Password is required.')]
    public string $password = '';


    public function login(LoginUserAction $action)
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->reset('password');
            throw $e;
        }

        $throttleKey = strtolower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many login attempts. Please try again in {$seconds} seconds.");
            $this->reset('password');
            return;
        }

        $result = $action->execute($this->email, $this->password);

        if (!$result['success']) {
            RateLimiter::hit($throttleKey);
            $this->addError('email', $result['message']);
            $this->addError('password', 'Please double-check your password.');
            $this->reset('password');
            return;
        }

        RateLimiter::clear($throttleKey);

        Auth::loginUsingId($result['user']['id'], false);
        session()->regenerate();

        return redirect()->intended(route('catalog.index'));
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
