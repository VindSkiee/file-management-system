<?php

namespace App\Repositories\Interfaces;

use App\Models\Folder;
use Illuminate\Support\Collection;

interface FolderRepositoryInterface
{
    public function getTree(): Collection;

    public function getByParent(?int $parentId, bool $withTrashed = false): Collection;

    public function count(): int;

    public function find(int $id): ?Folder;

    public function findWithTrashed(int $id): ?Folder;

    public function create(array $data): Folder;

    public function update(Folder $folder, array $data): Folder;

    public function delete(Folder $folder): bool;

    public function restore(Folder $folder): bool;

    /**
     * Collect every descendant folder id (recursive, including trashed).
     *
     * @return array<int, int>
     */
    public function getDescendantIds(int $folderId): array;

    public function deleteWithDescendants(Folder $folder, array $descendantIds): bool;

    public function restoreWithDescendants(Folder $folder, array $descendantIds): bool;
}
