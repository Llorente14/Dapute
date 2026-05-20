<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Actions\Auth\UpdateProfileAction;
use App\Helpers\AddressManager;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ProfileForm extends Component
{
    public string $email = '';
    public string $full_name = '';
    public string $phone_number = '';

    public bool $showToast = false;
    public string $toastMessage = '';
    public string $toastType = 'success'; // 'success' | 'error'

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->email = $user->email ?? '';
            $this->full_name = $user->full_name ?? $user->name ?? '';
            $this->phone_number = AddressManager::localPhoneNumber($user->phone_number ?? '');
        }
    }

    public function updateProfile(UpdateProfileAction $action)
    {
        $this->phone_number = AddressManager::localPhoneNumber($this->phone_number);

        $this->validate([
            'full_name' => 'required|string|max:200',
            'phone_number' => ['required', 'regex:/^\d{8,11}$/'],
        ], [
            'phone_number.regex' => 'Isi nomor HP setelah +62 dengan 8 sampai 11 digit angka.',
        ]);

        $result = $action->execute((string) Auth::id(), [
            'full_name' => $this->full_name,
            'phone_number' => AddressManager::normalizePhoneNumber($this->phone_number),
        ]);

        if ($result['success']) {
            $this->toastMessage = 'Profil berhasil diperbarui';
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

    public function updatedPhoneNumber(): void
    {
        $this->phone_number = AddressManager::localPhoneNumber($this->phone_number);
    }

    public function render()
    {
        return view('livewire.profile', ['user' => Auth::user()]);
    }
}
