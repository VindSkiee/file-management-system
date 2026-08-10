<?php

namespace App\Services;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    private const STORAGE_DISK = 'public';

    private const STORAGE_DIRECTORY = 'files';

    public function __construct(
        protected FileRepositoryInterface $fileRepository,
    ) {}

    public function paginateByFolder(?int $folderId, ?string $search = null, ?int $departmentId = null, bool $withTrashed = false, ?int $perPage = null): LengthAwarePaginator
    {
        return $this->fileRepository->paginateByFolder($folderId, $search, $departmentId, $withTrashed, $perPage);
    }

    public function find(int $id): File
    {
        return $this->findOrFail($id);
    }

    public function findWithTrashed(int $id): File
    {
        $file = $this->fileRepository->findWithTrashed($id);

        if ($file === null) {
            throw (new ModelNotFoundException)->setModel(File::class, $id);
        }

        return $file;
    }

    public function findMany(array $ids): Collection
    {
        return $this->fileRepository->findMany($ids);
    }

    public function create(array $data, UploadedFile $upload): File
    {
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

    public function deleteMany(array $ids): int
    {
        return $this->fileRepository->deleteMany($ids);
    }

    public function restore(File $file): void
    {
        $this->fileRepository->restore($file);
    }

    public function download(int $id): StreamedResponse
    {
        $file = $this->findOrFail($id);

        return Storage::disk(self::STORAGE_DISK)->download($file->file_path, $file->file_name);
    }

    public function downloadMany(array $ids): BinaryFileResponse
    {
        $files = $this->fileRepository->findMany($ids);

        $zipPath = tempnam(sys_get_temp_dir(), 'fms_').'.zip';
        $zip = new \ZipArchive;
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($files as $file) {
            $path = Storage::disk(self::STORAGE_DISK)->path($file->file_path);

            if (is_file($path)) {
                $zip->addFile($path, $file->file_name);
            }
        }

        $zip->close();

        return response()->download($zipPath, 'files.zip')->deleteFileAfterSend(true);
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
