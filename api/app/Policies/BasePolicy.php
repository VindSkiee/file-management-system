<?php

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    /**
     * Shared role check used by every policy in the FMS.
     * Roles are seeded by name, so the name is the source of truth
     * (IDs are auto-increment and not guaranteed to be stable).
     */
    protected function isAdministrator(User $user): bool
    {
        return $user->role?->name === 'Administrator';
    }
}
