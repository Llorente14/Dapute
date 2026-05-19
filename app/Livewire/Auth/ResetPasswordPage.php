<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Actions\Auth\UpdatePasswordAction;

#[Layout('layouts.guest')]
class ResetPasswordPage extends Component
{
    #[Validate('required|min:6|same:passwordConfirmation', message: [
        'required' => 'Password is required.',
        'min' => 'Password must be at least 6 characters.',
        'same' => 'Passwords do not match.'
    ])]
    public string $password = '';

    public string $passwordConfirmation = '';

    public bool $isSuccess = false;

    public function updatePassword(UpdatePasswordAction $action, string $accessToken)
    {
        if (empty($accessToken)) {
            $this->addError('password', 'Invalid or missing recovery token. Please try clicking the link in your email again.');
            return;
        }

        $this->validate();

        $result = $action->execute($accessToken, $this->password);

        if (!$result['success']) {
            $this->addError('password', $result['message']);
            return;
        }

        $this->isSuccess = true;
    }

    public function render()
    {
        return view('livewire.auth.reset-password-page');
    }
}
