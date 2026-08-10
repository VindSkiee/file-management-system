<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        $stats = $this->dashboardService->getStats();

        return response()->json([
            'total_folders' => $stats['total_folders'],
            'total_files' => $stats['total_files'],
            'total_departments' => $stats['total_departments'],
            'recent_files' => FileResource::collection($stats['recent_files']),
        ]);
    }
}
