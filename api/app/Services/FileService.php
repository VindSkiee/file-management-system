<?php

namespace App\Services;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileService
{
    private const STORAGE_DISK = 'public';

    private const STORAGE_DIRECTORY = 'files';

    public function __construct(
        protected FileRepositoryInterface $fileRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->fileRepository->getAll();
    }

    public function getByFolder(?int $folderId): Collection
    {
        return $this->fileRepository->getByFolder($folderId);
    }

    public function find(int $id): File
    {
        return $this->findOrFail($id);
    }

    public function create(array $data, UploadedFile $upload): File
    {
        // store() generates a unique hashed file name on disk; the original
        // client-side name is preserved separately in the file_name column.
        $filePath = $upload->store(self::STORAGE_DIRECTORY, self::STORAGE_DISK);

        return $this->fileRepository->create([
            'folder_id' => $data['folder_id'] ?? null,
            'department_id' => $data['department_id'],
            'uploaded_by' => Auth::id(),
            'title' => $data['title'],
            'file_name' => $upload->getClientOriginalName(),
            'file_path' => $filePath,
        ]);
    }

    public function update(int $id, array $data, ?UploadedFile $newFile): File
    {
        $file = $this->findOrFail($id);

        if ($newFile !== null) {
            Storage::disk(self::STORAGE_DISK)->delete($file->file_path);

            $data['file_path'] = $newFile->store(self::STORAGE_DIRECTORY, self::STORAGE_DISK);
            $data['file_name'] = $newFile->getClientOriginalName();
        }

        return $this->fileRepository->update($file, $data);
    }

    public function delete(int $id): void
    {
        $file = $this->findOrFail($id);

        $this->fileRepository->delete($file);
    }

    public function download(int $id): BinaryFileResponse
    {
        $file = $this->findOrFail($id);

        return Storage::disk(self::STORAGE_DISK)->download($file->file_path, $file->file_name);
    }

    private function findOrFail(int $id): File
    {
        $file = $this->fileRepository->find($id);

        if ($file === null) {
            throw (new ModelNotFoundException)->setModel(File::class, $id);
        }

        return $file;
    }
}
