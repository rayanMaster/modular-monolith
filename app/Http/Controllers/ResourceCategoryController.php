<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\ResourceCategoryCreateRequest;
use App\Http\Requests\ResourceCategoryUpdateRequest;
use App\Http\Resources\ResourceCategoryDetailsResource;
use App\Http\Resources\ResourceCategoryListResource;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Http\JsonResponse;

class ResourceCategoryController extends Controller
{
    public function list(): JsonResponse
    {
        $resourceCategories = ResourceCategory::all();

        return ApiResponseHelper::sendResponse(new Result(ResourceCategoryListResource::collection($resourceCategories)));
    }

    public function store(ResourceCategoryCreateRequest $request): JsonResponse
    {
        ResourceCategory::query()->create($request->validated());

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function show(int $resourceId, int $resourceCategoryId): JsonResponse
    {
        Resource::query()->findOrFail($resourceId);
        $resourceCategory = ResourceCategory::query()->findOrFail($resourceCategoryId);

        return ApiResponseHelper::sendResponse(new Result(ResourceCategoryDetailsResource::make($resourceCategory)));

    }

    public function update(ResourceCategoryUpdateRequest $request, int $resourceId, int $resourceCategoryId): JsonResponse
    {

        Resource::query()->findOrFail($resourceId);
        $resourceCategory = ResourceCategory::query()->findOrFail($resourceCategoryId);
        $resourceCategory->update($request->validated());

        return ApiResponseHelper::sendSuccessResponse();

    }

    public function destroy(int $resourceId, int $resourceCategoryId): JsonResponse
    {
        Resource::query()->findOrFail($resourceId);
        $resourceCategory = ResourceCategory::query()->findOrFail($resourceCategoryId);
        $resourceCategory->delete();

        return ApiResponseHelper::sendSuccessResponse();
    }
}
