<?php

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    protected function isAdministrator(User $user): bool
    {
        return $user->role?->name === 'Administrator';
    }
}
