<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorksiteCategoryCreateRequest;
use App\Http\Requests\WorksiteCategoryUpdateRequest;
use App\Http\Resources\WorksiteCategoryDetailsResource;
use App\Http\Resources\WorksiteCategoryListResource;
use App\Models\WorksiteCategory;
use Illuminate\Http\JsonResponse;

class WorksiteCategoryController extends Controller
{
    public function list(): JsonResponse
    {
        $categories = WorksiteCategory::query()->get();

        return ApiResponseHelper::sendSuccessResponse(new Result(WorksiteCategoryListResource::collection($categories)));
    }

    public function store(WorksiteCategoryCreateRequest $request): JsonResponse
    {
        WorksiteCategory::query()->create($request->validated());

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function show(int $id): JsonResponse
    {
        $category = WorksiteCategory::query()->findOrFail($id);

        return ApiResponseHelper::sendSuccessResponse(new Result(WorksiteCategoryDetailsResource::make($category)));
    }

    public function update(WorksiteCategoryUpdateRequest $request, int $id): JsonResponse
    {

        $workSiteCategory = WorksiteCategory::query()->findOrFail($id);
        $workSiteCategory->update($request->validated());

        return ApiResponseHelper::sendSuccessResponse();

    }

    public function destroy(int $id): JsonResponse
    {
        $workSiteCategory = WorksiteCategory::query()->findOrFail($id);
        $workSiteCategory->delete();

        return ApiResponseHelper::sendSuccessResponse();
    }
}
