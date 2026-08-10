<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ActivityLog::class);

        $logs = $this->activityLogService->getPaginated($request->integer('per_page') ?: null);

        return ActivityLogResource::collection($logs)->response();
    }
}
