<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FileRepository implements FileRepositoryInterface
{
    public function paginateByFolder(?int $folderId, ?string $search = null, ?int $departmentId = null, bool $withTrashed = false, ?int $perPage = null): LengthAwarePaginator
    {
        return File::query()
            ->with(['folder', 'department', 'uploader'])
            ->when($withTrashed, fn ($query) => $query->withTrashed())
            ->where('folder_id', $folderId)
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('file_name', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($departmentId !== null, fn ($query) => $query->where('department_id', $departmentId))
            ->latest()
            ->paginate($perPage ?? 15);
    }

    public function getLatest(int $limit): Collection
    {
        return File::query()
            ->with(['folder', 'department', 'uploader'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function count(): int
    {
        return File::query()->count();
    }

    public function find(int $id): ?File
    {
        return File::query()
            ->with(['folder', 'department', 'uploader'])
            ->find($id);
    }

    public function findWithTrashed(int $id): ?File
    {
        return File::query()
            ->with(['folder', 'department', 'uploader'])
            ->withTrashed()
            ->find($id);
    }

    public function create(array $data): File
    {
        return File::query()->create($data);
    }

    public function update(File $file, array $data): File
    {
        $file->update($data);

        return $file;
    }

    public function delete(File $file): bool
    {
        return (bool) $file->delete();
    }

    public function restore(File $file): bool
    {
        return (bool) $file->restore();
    }
}
