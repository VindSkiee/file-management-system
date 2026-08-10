<?php

namespace App\Repositories\Interfaces;

use App\Models\File;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    public function getAll(): Collection;

    public function getByFolder(?int $folderId): Collection;

    public function getLatest(int $limit): Collection;

    public function count(): int;

    public function find(int $id): ?File;

    public function create(array $data): File;

    public function update(File $file, array $data): File;

    public function delete(File $file): bool;
}
