<?php

namespace App\Livewire\Admin;

use App\Actions\Auth\UpdateUserRoleAction;
use App\Actions\Auth\AdminCreateUserAction;
use App\Actions\Auth\AdminUpdateUserAction;
use App\Actions\Auth\AdminDeleteUserAction;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Url;

class UserManagement extends Component
{
    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'All Roles')]
    public $filterRole = 'All Roles';
    public $create_full_name = '';
    public $create_email = '';
    public $create_phone_number = '';
    public $create_password = '';

    public $edit_id = null;
    public $edit_full_name = '';
    public $edit_email = '';
    public $edit_phone_number = '';
    public $edit_role = 'admin';
    public $edit_is_active = true;

    public function resetCreateForm()
    {
        $this->reset(['create_full_name', 'create_email', 'create_phone_number', 'create_password']);
    }

    public function createUser(AdminCreateUserAction $action)
    {
        $this->validate([
            'create_full_name' => 'required|string|max:255',
            'create_email' => 'required|email|max:255',
            'create_password' => 'required|string|min:8',
        ]);

        $result = $action->execute([
            'full_name' => $this->create_full_name,
            'email' => $this->create_email,
            'phone_number' => $this->create_phone_number,
            'password' => $this->create_password,
        ]);

        if ($result['success']) {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'success']);
            $this->dispatch('close-modal-create');
            $this->resetCreateForm();
        } else {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
        }
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->edit_id = $user->id;
        $this->edit_full_name = $user->full_name;
        $this->edit_email = $user->email;
        $this->edit_phone_number = $user->phone_number;
        $this->edit_role = $user->role;
        $this->edit_is_active = $user->is_active;
    }

    public function updateUser(AdminUpdateUserAction $action)
    {
        $this->validate([
            'edit_full_name' => 'required|string|max:255',
            'edit_email' => 'required|email|max:255',
        ]);

        $result = $action->execute($this->edit_id, [
            'full_name' => $this->edit_full_name,
            'email' => $this->edit_email,
            'phone_number' => $this->edit_phone_number,
            'role' => $this->edit_role,
            'is_active' => $this->edit_is_active,
        ]);

        if ($result['success']) {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'success']);
            $this->dispatch('close-modal-edit');
        } else {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
        }
    }

    public function deleteUser($userId, AdminDeleteUserAction $action)
    {
        $result = $action->execute($userId);
        if ($result['success']) {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
        }
    }

    public function resetUserPassword($userId, UpdateUserRoleAction $action)
    {
        $user = User::findOrFail($userId);
        $result = $action->sendPasswordReset($user->email);

        if ($result['success']) {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => $result['message'], 'type' => 'error']);
        }
    }
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
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('email', 'ilike', '%' . $this->search . '%')
                  ->orWhere('phone_number', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->filterRole !== 'All Roles') {
            if ($this->filterRole === 'admin') {
                $query->where('role', 'admin');
            } else {
                $query->where('role', strtolower($this->filterRole));
            }
        }

        $users = $query->orderBy('created_at', 'desc')->get();
        return view('livewire.dashboard.user-management-table', [
            'users' => $users
        ])->layout('layouts.admin');
    }
}
