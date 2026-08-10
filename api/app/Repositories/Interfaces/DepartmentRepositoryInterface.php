<?php

namespace App\Repositories\Interfaces;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DepartmentRepositoryInterface
{
    public function getAll(bool $withTrashed = false): Collection;

    public function paginate(bool $withTrashed = false, ?int $perPage = null): LengthAwarePaginator;

    public function count(): int;

    public function find(int $id): ?Department;

    public function findWithTrashed(int $id): ?Department;

    public function create(array $data): Department;

    public function update(Department $department, array $data): Department;

    public function delete(Department $department): bool;

    public function restore(Department $department): bool;
}
