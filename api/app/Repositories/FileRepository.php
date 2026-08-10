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
