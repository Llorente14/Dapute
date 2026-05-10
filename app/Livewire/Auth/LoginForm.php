<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Actions\Auth\LoginUserAction;

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
        $this->validate();

        $result = $action->execute($this->email, $this->password);

        if (!$result['success']) {
            $this->addError('email', $result['message']);
            $this->addError('password', 'Periksa kembali password Anda.');
            return;
        }

        Auth::loginUsingId($result['user']['id']);

        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
