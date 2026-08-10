<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, File $file): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update(User $user, File $file): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete(User $user, File $file): bool
    {
        return $this->isAdministrator($user);
    }

    public function restore(User $user, File $file): bool
    {
        return $this->isAdministrator($user);
    }

    public function download(User $user, File $file): bool
    {
        return true;
    }
}
