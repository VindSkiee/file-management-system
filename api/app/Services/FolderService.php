<?php

namespace App\Services;

use App\Models\Folder;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class FolderService
{
    public function __construct(
        protected FolderRepositoryInterface $folderRepository,
    ) {}

    public function getTree(): Collection
    {
        return $this->folderRepository->getTree();
    }

    public function paginateByParent(?int $parentId, bool $withTrashed = false, ?int $perPage = null): LengthAwarePaginator
    {
        return $this->folderRepository->paginateByParent($parentId, $withTrashed, $perPage);
    }

    public function find(int $id): Folder
    {
        return $this->findOrFail($id);
    }

    public function findWithTrashed(int $id): Folder
    {
        $folder = $this->folderRepository->findWithTrashed($id);

        if ($folder === null) {
            throw (new ModelNotFoundException)->setModel(Folder::class, $id);
        }

        return $folder;
    }

    public function create(array $data): Folder
    {
        $this->ensureParentExists($data['parent_id'] ?? null);

        return $this->folderRepository->create($data);
    }

    public function update(int $id, array $data): Folder
    {
        $folder = $this->findOrFail($id);

        return $this->folderRepository->update($folder, $data);
    }

    public function delete(int $id): void
    {
        $folder = $this->findOrFail($id);

        $descendantIds = $this->folderRepository->getDescendantIds($folder->id);
        $this->folderRepository->deleteWithDescendants($folder, $descendantIds);
    }

    public function restore(Folder $folder): void
    {
        $descendantIds = $this->folderRepository->getDescendantIds($folder->id);
        $this->folderRepository->restoreWithDescendants($folder, $descendantIds);
    }

    private function findOrFail(int $id): Folder
    {
        $folder = $this->folderRepository->find($id);

        if ($folder === null) {
            throw (new ModelNotFoundException)->setModel(Folder::class, $id);
        }

        return $folder;
    }

    private function ensureParentExists(?int $parentId): void
    {
        if ($parentId === null) {
            return;
        }

        if ($this->folderRepository->find($parentId) === null) {
            throw ValidationException::withMessages([
                'parent_id' => 'Folder induk yang dipilih tidak ditemukan.',
            ]);
        }
    }
}
