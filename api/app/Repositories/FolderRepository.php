<?php

namespace App\Repositories;

use App\Models\Folder;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use Illuminate\Support\Collection;

class FolderRepository implements FolderRepositoryInterface
{
    public function getTree(): Collection
    {
        // Root folders (parent_id = NULL) eager-loaded with their recursive
        // descendants. Soft-deleted children are excluded automatically.
        return Folder::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): ?Folder
    {
        return Folder::query()->find($id);
    }

    public function create(array $data): Folder
    {
        return Folder::query()->create($data);
    }

    public function update(Folder $folder, array $data): Folder
    {
        $folder->update($data);

        return $folder;
    }

    public function delete(Folder $folder): bool
    {
        // Eloquent soft delete: keeps the hierarchy history and never breaks
        // the self-referencing foreign key.
        return (bool) $folder->delete();
    }
}
