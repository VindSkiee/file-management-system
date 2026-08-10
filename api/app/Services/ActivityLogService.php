<?php

namespace App\Services;

use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    public function __construct(
        protected ActivityLogRepositoryInterface $activityLogRepository,
    ) {}

    public function getPaginated(?int $perPage): LengthAwarePaginator
    {
        return $this->activityLogRepository->paginate($perPage);
    }
}
