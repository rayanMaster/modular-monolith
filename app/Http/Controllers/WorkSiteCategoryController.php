<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteCategoryCreateRequest;
use App\Http\Requests\WorkSiteCategoryUpdateRequest;
use App\Http\Resources\WorkSiteCategoryDetailsResource;
use App\Http\Resources\WorkSiteCategoryListResource;
use App\Models\WorkSiteCategory;

class WorkSiteCategoryController extends Controller
{
    public function list()
    {
        $categories = WorkSiteCategory::query()->get();

        return ApiResponseHelper::sendResponse(new Result(WorkSiteCategoryListResource::collection($categories)));
    }

    public function create(WorkSiteCategoryCreateRequest $request)
    {
        WorkSiteCategory::query()->create($request->validated());
    }

    public function store()
    {

    }

    public function show($id)
    {
        $category = WorkSiteCategory::query()->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(WorkSiteCategoryDetailsResource::make($category)));
    }

    public function edit($id)
    {

    }

    public function update(WorkSiteCategoryUpdateRequest $request, int $id)
    {

        $workSiteCategory = WorkSiteCategory::query()->findOrFail($id);
        $workSiteCategory->update(['name' => $request->name]);

    }

    public function destroy($id)
    {
        $workSiteCategory = WorkSiteCategory::query()->findOrFail($id);
        $workSiteCategory->delete();
    }
}
