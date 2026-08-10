<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
    ) {}

    public function index(): JsonResponse
    {
        $files = $this->fileService->getAll();

        return FileResource::collection($files)->response();
    }

    public function store(StoreFileRequest $request): JsonResponse
    {
        $file = $this->fileService->create($request->validated(), $request->file('file'));

        return (new FileResource($file))->response()->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        $file = $this->fileService->find($id);

        return (new FileResource($file))->response();
    }

    public function update(UpdateFileRequest $request, int $id): JsonResponse
    {
        $file = $this->fileService->update($id, $request->validated(), $request->file('file'));

        return (new FileResource($file))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->fileService->delete($id);

        return response()->noContent();
    }

    public function download(int $id): BinaryFileResponse
    {
        return $this->fileService->download($id);
    }
}
