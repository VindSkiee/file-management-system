<?php

namespace App\Repositories\Interfaces;

use App\Models\Folder;
use Illuminate\Support\Collection;

interface FolderRepositoryInterface
{
    public function getTree(): Collection;

    public function find(int $id): ?Folder;

    public function create(array $data): Folder;

    public function update(Folder $folder, array $data): Folder;

    public function delete(Folder $folder): bool;
}
