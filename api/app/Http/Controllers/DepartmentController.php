<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService,
    ) {}

    public function index(): JsonResponse
    {
        $departments = $this->departmentService->getAll();

        return DepartmentResource::collection($departments)->response();
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = $this->departmentService->create($request->validated());

        return (new DepartmentResource($department))->response()->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        $department = $this->departmentService->find($id);

        return (new DepartmentResource($department))->response();
    }

    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        $department = $this->departmentService->update($id, $request->validated());

        return (new DepartmentResource($department))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->departmentService->delete($id);

        return response()->noContent();
    }
}
