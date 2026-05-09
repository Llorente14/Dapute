<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class RegisterForm extends Component
{
    public function render()
    {
        return view('livewire.auth.register-form');
    }
}
