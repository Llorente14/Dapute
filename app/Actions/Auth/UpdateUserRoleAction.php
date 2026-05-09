<?php

namespace App\Actions\Auth;

class UpdateUserRoleAction
{
    public function __invoke(int $userId, string $role): void
    {
        // TODO: validate role in ['customer','staff','owner'], update DB
    }
}
