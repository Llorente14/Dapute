<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Actions\Auth\UpdateProfileAction;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class ProfileForm extends Component
{
    public string $email = '';
    public string $first_name = '';
    public string $last_name = '';
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
            $names = explode(' ', $user->full_name ?? $user->name ?? '', 2);
            $this->first_name = $names[0] ?? '';
            $this->last_name = $names[1] ?? '';
            $this->phone_number = $user->phone_number ?? '';
            $this->address = $user->address ?? '';
        }
    }

    public function updateProfile(UpdateProfileAction $action)
    {
        $this->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $fullName = trim($this->first_name . ' ' . $this->last_name);

        $result = $action->execute((string) Auth::id(), [
            'full_name' => $fullName,
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
