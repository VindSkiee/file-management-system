<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', File::class);

        $folderId = $request->integer('folder_id') ?: null;
        $search = $request->query('search') ?: null;
        $departmentId = $request->integer('department_id') ?: null;
        $withTrashed = $request->boolean('trashed');

        $files = $this->fileService->paginateByFolder($folderId, $search, $departmentId, $withTrashed, $request->integer('per_page') ?: null);

        return FileResource::collection($files)->response();
    }

    public function store(StoreFileRequest $request): JsonResponse
    {
        Gate::authorize('create', File::class);

        $file = $this->fileService->create($request->validated(), $request->file('file'));

        return (new FileResource($file))->response()->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        $file = $this->fileService->find($id);
        Gate::authorize('view', $file);

        return (new FileResource($file))->response();
    }

    public function update(UpdateFileRequest $request, int $id): JsonResponse
    {
        $file = $this->fileService->find($id);
        Gate::authorize('update', $file);

        $file = $this->fileService->update($id, $request->validated(), $request->file('file'));

        return (new FileResource($file))->response();
    }

    public function destroy(int $id): Response
    {
        $file = $this->fileService->find($id);
        Gate::authorize('delete', $file);

        $this->fileService->delete($id);

        return response()->noContent();
    }

    public function restore(int $id): JsonResponse
    {
        $file = $this->fileService->findWithTrashed($id);
        Gate::authorize('restore', $file);

        $this->fileService->restore($file);

        return (new FileResource($file))->response();
    }

    public function download(int $id): StreamedResponse
    {
        $file = $this->fileService->find($id);
        Gate::authorize('download', $file);

        return $this->fileService->download($id);
    }
}
