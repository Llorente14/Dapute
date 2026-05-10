<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Actions\Auth\UpdateProfileAction;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ProfileForm extends Component
{
    public string $email = '';
    public string $full_name = '';
    public string $phone_number = '';
    public string $address = '';

    public bool $showToast = false;
    public string $toastMessage = '';
    public string $toastType = 'success'; // 'success' | 'error'

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->email = $user->email ?? '';
            $this->full_name = $user->full_name ?? $user->name ?? '';
            $this->phone_number = $user->phone_number ?? '';
            $this->address = $user->address ?? '';
        }
    }

    public function updateProfile(UpdateProfileAction $action)
    {
        $this->validate([
            'full_name' => 'required|string|max:200',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $result = $action->execute((string) Auth::id(), [
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
        ]);

        if ($result['success']) {
            $this->toastMessage = 'Profil & Alamat berhasil diperbarui';
            $this->toastType = 'success';
            $this->showToast = true;
            $this->dispatch('profile-updated');
        } else {
            $this->toastMessage = $result['message'];
            $this->toastType = 'error';
            $this->showToast = true;
        }
    }

    public function dismissToast()
    {
        $this->showToast = false;
    }

    public function render()
    {
        return view('profile', ['user' => Auth::user()]);
    }
}
