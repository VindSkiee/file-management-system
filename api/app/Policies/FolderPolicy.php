<?php

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;

class FolderPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Folder $folder): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update(User $user, Folder $folder): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete(User $user, Folder $folder): bool
    {
        return $this->isAdministrator($user);
    }
}
