<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\ResourceCategoryCreateRequest;
use App\Http\Requests\ResourceCategoryUpdateRequest;
use App\Http\Resources\ResourceCategoryDetailsResource;
use App\Http\Resources\ResourceCategoryListResource;
use App\Models\ResourceCategory;

class ResourceCategoryController extends Controller
{
    public function list()
    {
        $resourceCategories = ResourceCategory::all();

        return ApiResponseHelper::sendResponse(new Result(ResourceCategoryListResource::collection($resourceCategories)));
    }

    public function store(ResourceCategoryCreateRequest $request)
    {
        ResourceCategory::query()->create($request->validated());
    }

    public function show($resourceId,$resourceCategoryId)
    {
        $resourceCategory = ResourceCategory::query()->findOrFail($resourceCategoryId);

        return ApiResponseHelper::sendResponse(new Result(ResourceCategoryDetailsResource::make($resourceCategory)));

    }

    public function update(ResourceCategoryUpdateRequest $request, $resourceId,$resourceCategoryId)
    {

        $resourceCategory = ResourceCategory::query()->findOrFail($resourceCategoryId);
        $resourceCategory->update($request->validated());

    }

    public function destroy($resourceId,$resourceCategoryId)
    {
        $resourceCategory = ResourceCategory::query()->findOrFail($resourceCategoryId);
        $resourceCategory->delete();
    }
}
