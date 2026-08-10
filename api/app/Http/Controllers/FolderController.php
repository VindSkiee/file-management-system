<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FolderController extends Controller
{
    public function __construct(
        protected FolderService $folderService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Folder::class);

        $parentId = $request->integer('parent_id') ?: null;

        $folders = $this->folderService->getByParent($parentId);

        return FolderResource::collection($folders)->response();
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        Gate::authorize('create', Folder::class);

        $folder = $this->folderService->create($request->validated());

        return (new FolderResource($folder))->response()->setStatusCode(201);
    }

    public function update(UpdateFolderRequest $request, int $id): JsonResponse
    {
        $folder = $this->folderService->find($id);
        Gate::authorize('update', $folder);

        $folder = $this->folderService->update($id, $request->validated());

        return (new FolderResource($folder))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $folder = $this->folderService->find($id);
        Gate::authorize('delete', $folder);

        $this->folderService->delete($id);

        return response()->noContent();
    }
}
