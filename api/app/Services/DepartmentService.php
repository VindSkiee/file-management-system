<?php

namespace App\Services;

use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class DepartmentService
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository,
    ) {}

    public function getAll(bool $withTrashed = false): Collection
    {
        return $this->departmentRepository->getAll($withTrashed);
    }

    public function find(int $id): Department
    {
        return $this->findOrFail($id);
    }

    public function findWithTrashed(int $id): Department
    {
        $department = $this->departmentRepository->findWithTrashed($id);

        if ($department === null) {
            throw (new ModelNotFoundException)->setModel(Department::class, $id);
        }

        return $department;
    }

    public function create(array $data): Department
    {
        return $this->departmentRepository->create($data);
    }

    public function update(int $id, array $data): Department
    {
        $department = $this->findOrFail($id);

        return $this->departmentRepository->update($department, $data);
    }

    public function delete(int $id): void
    {
        $department = $this->findOrFail($id);

        $this->departmentRepository->delete($department);
    }

    public function restore(Department $department): void
    {
        $this->departmentRepository->restore($department);
    }

    private function findOrFail(int $id): Department
    {
        $department = $this->departmentRepository->find($id);

        if ($department === null) {
            throw (new ModelNotFoundException)->setModel(Department::class, $id);
        }

        return $department;
    }
}
