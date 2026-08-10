<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Support\Collection;

class FileRepository implements FileRepositoryInterface
{
    public function getAll(): Collection
    {
        return File::query()
            ->with(['folder', 'department', 'uploader'])
            ->latest()
            ->get();
    }

    public function getByFolder(?int $folderId): Collection
    {
        // NULL folder_id resolves to "WHERE folder_id IS NULL" (root files).
        return File::query()
            ->with(['folder', 'department', 'uploader'])
            ->where('folder_id', $folderId)
            ->latest()
            ->get();
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
        // Eloquent soft delete: the physical file stays on disk until a force
        // delete is explicitly requested.
        return (bool) $file->delete();
    }
}
