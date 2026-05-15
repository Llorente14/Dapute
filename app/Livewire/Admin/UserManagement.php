<?php

namespace App\Livewire\Admin;

use App\Actions\Auth\UpdateUserRoleAction;
use App\Models\User;
use Livewire\Component;

class UserManagement extends Component
{
    public function toggleUserStatus($userId, $isActive, UpdateUserRoleAction $action)
    {
        try {
            $user = User::findOrFail($userId);
            $result = $action->update($userId, $user->role, filter_var($isActive, FILTER_VALIDATE_BOOLEAN));

            if ($result['success']) {
                $this->dispatch('notify', ['message' => $result['message'], 'type' => 'success']);
            } else {
                $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function updateUserRole($userId, $role, UpdateUserRoleAction $action)
    {
        try {
            $user = User::findOrFail($userId);
            $result = $action->update($userId, $role, $user->is_active);

            if ($result['success']) {
                $this->dispatch('notify', ['message' => $result['message'], 'type' => 'success']);
            } else {
                $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('livewire.dashboard.user-management-table', [
            'users' => $users
        ])->layout('layouts.admin');
    }
}
