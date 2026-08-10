<?php

namespace App\Services;

use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use Illuminate\Support\Collection;

class DashboardService
{
    private const RECENT_FILES_LIMIT = 10;

    public function __construct(
        protected FolderRepositoryInterface $folderRepository,
        protected FileRepositoryInterface $fileRepository,
        protected DepartmentRepositoryInterface $departmentRepository,
    ) {}

    /**
     * @return array{total_folders: int, total_files: int, total_departments: int, recent_files: Collection}
     */
    public function getStats(): array
    {
        return [
            'total_folders' => $this->folderRepository->count(),
            'total_files' => $this->fileRepository->count(),
            'total_departments' => $this->departmentRepository->count(),
            'recent_files' => $this->fileRepository->getLatest(self::RECENT_FILES_LIMIT),
        ];
    }
}
