<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Http\Resources\FolderResource;
use App\Services\FolderService;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    public function __construct(
        protected FolderService $folderService,
    ) {}

    public function index(): JsonResponse
    {
        $tree = $this->folderService->getTree();

        return FolderResource::collection($tree)->response();
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        $folder = $this->folderService->create($request->validated());

        return (new FolderResource($folder))->response()->setStatusCode(201);
    }

    public function update(UpdateFolderRequest $request, int $id): JsonResponse
    {
        $folder = $this->folderService->update($id, $request->validated());

        return (new FolderResource($folder))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->folderService->delete($id);

        return response()->noContent();
    }
}
