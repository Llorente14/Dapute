<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Actions\Auth\SendPasswordResetLinkAction;

#[Layout('layouts.guest')]
class ForgotPasswordPage extends Component
{
    #[Validate('required|email', message: [
        'required' => 'Email address is required.',
        'email' => 'Invalid email address.'
    ])]
    public string $email = '';

    public bool $isSuccess = false;

    public function sendResetLink(SendPasswordResetLinkAction $action)
    {
        $this->validate();

        $result = $action->execute($this->email);

        if (!$result['success']) {
            $this->addError('email', $result['message']);
            return;
        }

        $this->isSuccess = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-page');
    }
}
