<?php

namespace App\Repositories\Interfaces;

use App\Models\File;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    public function paginateByFolder(?int $folderId, ?string $search = null, ?int $departmentId = null, bool $withTrashed = false, ?int $perPage = null): LengthAwarePaginator;

    public function getLatest(int $limit): Collection;

    public function count(): int;

    public function find(int $id): ?File;

    public function findWithTrashed(int $id): ?File;

    public function findMany(array $ids): Collection;

    public function create(array $data): File;

    public function update(File $file, array $data): File;

    public function delete(File $file): bool;

    public function deleteMany(array $ids): int;

    public function restore(File $file): bool;
}
