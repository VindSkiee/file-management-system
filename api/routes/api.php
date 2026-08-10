<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (require valid Sanctum token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);

    Route::apiResource('departments', DepartmentController::class);
    Route::post('/departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');

    Route::apiResource('folders', FolderController::class)->except(['show']);
    Route::post('/folders/{folder}/restore', [FolderController::class, 'restore'])->name('folders.restore');

    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::post('/files/{file}/restore', [FileController::class, 'restore'])->name('files.restore');
    Route::apiResource('files', FileController::class);
});
