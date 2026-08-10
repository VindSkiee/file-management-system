<?php

namespace App\Repositories\Interfaces;

use App\Models\Folder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FolderRepositoryInterface
{
    public function getTree(): Collection;

    public function paginateByParent(?int $parentId, bool $withTrashed = false, ?int $perPage = null): LengthAwarePaginator;

    public function count(): int;

    public function find(int $id): ?Folder;

    public function findWithTrashed(int $id): ?Folder;

    public function create(array $data): Folder;

    public function update(Folder $folder, array $data): Folder;

    public function delete(Folder $folder): bool;

    public function restore(Folder $folder): bool;

    /**
     * @return array<int, int>
     */
    public function getDescendantIds(int $folderId): array;

    public function deleteWithDescendants(Folder $folder, array $descendantIds): bool;

    public function restoreWithDescendants(Folder $folder, array $descendantIds): bool;
}
