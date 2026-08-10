<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function paginate(?int $perPage): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->with('user')
            ->latest()
            ->paginate($perPage ?? 20);
    }
}
