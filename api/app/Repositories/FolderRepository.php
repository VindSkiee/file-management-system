<?php

namespace App\Repositories;

use App\Models\File;
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

    public function getByParent(?int $parentId, bool $withTrashed = false): Collection
    {
        // NULL parent_id resolves to "WHERE parent_id IS NULL" (root folders).
        return Folder::query()
            ->when($withTrashed, fn ($query) => $query->withTrashed())
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
    }

    public function count(): int
    {
        return Folder::query()->count();
    }

    public function find(int $id): ?Folder
    {
        return Folder::query()->find($id);
    }

    public function findWithTrashed(int $id): ?Folder
    {
        return Folder::query()->withTrashed()->find($id);
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

    public function restore(Folder $folder): bool
    {
        return (bool) $folder->restore();
    }

    public function getDescendantIds(int $folderId): array
    {
        $ids = [];

        $children = Folder::query()
            ->withTrashed()
            ->where('parent_id', $folderId)
            ->pluck('id');

        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }

        return array_values(array_unique($ids));
    }

    public function deleteWithDescendants(Folder $folder, array $descendantIds): bool
    {
        $folderIds = array_merge([$folder->id], $descendantIds);

        // Soft delete (logical cascade): soft delete bypasses the FK
        // "ON DELETE CASCADE", so files inside every folder in the subtree
        // must be soft-deleted explicitly.
        File::query()->whereIn('folder_id', $folderIds)->delete();
        Folder::query()->whereIn('id', $folderIds)->delete();

        return true;
    }

    public function restoreWithDescendants(Folder $folder, array $descendantIds): bool
    {
        $folderIds = array_merge([$folder->id], $descendantIds);

        File::query()->withTrashed()->whereIn('folder_id', $folderIds)->restore();
        Folder::query()->withTrashed()->whereIn('id', $folderIds)->restore();

        return true;
    }
}
