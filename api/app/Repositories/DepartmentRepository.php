<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use Illuminate\Support\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function getAll(): Collection
    {
        return Department::query()
            ->orderBy('name')
            ->get();
    }

    public function count(): int
    {
        return Department::query()->count();
    }

    public function find(int $id): ?Department
    {
        return Department::query()->find($id);
    }

    public function create(array $data): Department
    {
        return Department::query()->create($data);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);

        return $department;
    }

    public function delete(Department $department): bool
    {
        // Eloquent soft delete: records remain in DB, hidden from default queries.
        return (bool) $department->delete();
    }
}
