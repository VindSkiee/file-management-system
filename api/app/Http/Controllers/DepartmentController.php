<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Department::class);

        $departments = $this->departmentService->paginate($request->boolean('trashed'), $request->integer('per_page') ?: null);

        return DepartmentResource::collection($departments)->response();
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        Gate::authorize('create', Department::class);

        $department = $this->departmentService->create($request->validated());

        return (new DepartmentResource($department))->response()->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        $department = $this->departmentService->find($id);
        Gate::authorize('view', $department);

        return (new DepartmentResource($department))->response();
    }

    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        $department = $this->departmentService->find($id);
        Gate::authorize('update', $department);

        $department = $this->departmentService->update($id, $request->validated());

        return (new DepartmentResource($department))->response();
    }

    public function destroy(int $id): Response
    {
        $department = $this->departmentService->find($id);
        Gate::authorize('delete', $department);

        $this->departmentService->delete($id);

        return response()->noContent();
    }

    public function restore(int $id): JsonResponse
    {
        $department = $this->departmentService->findWithTrashed($id);
        Gate::authorize('restore', $department);

        $this->departmentService->restore($department);

        return (new DepartmentResource($department))->response();
    }
}
