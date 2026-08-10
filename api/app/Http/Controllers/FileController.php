<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
    ) {}

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', File::class);

        $files = $this->fileService->getAll();

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

    public function destroy(int $id): JsonResponse
    {
        $file = $this->fileService->find($id);
        Gate::authorize('delete', $file);

        $this->fileService->delete($id);

        return response()->noContent();
    }

    public function download(int $id): BinaryFileResponse
    {
        $file = $this->fileService->find($id);
        Gate::authorize('download', $file);

        return $this->fileService->download($id);
    }
}
