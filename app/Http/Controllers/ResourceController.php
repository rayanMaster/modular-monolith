<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\ResourceCreateRequest;
use App\Http\Requests\ResourceUpdateRequest;
use App\Http\Resources\ResourceDetailsResource;
use App\Http\Resources\ResourceListResource;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(): JsonResponse
    {
        $resources = Resource::query()->get();

        return ApiResponseHelper::sendResponse(new Result(ResourceListResource::collection($resources)));
    }

    public function store(ResourceCreateRequest $request): JsonResponse
    {
        Resource::query()->create($request->validated());

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $resource = Resource::query()->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(ResourceDetailsResource::make($resource)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResourceUpdateRequest $request, int $id): JsonResponse
    {
        $resource = Resource::query()->findOrFail($id);
        $resource->update($request->validated());

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $resource = Resource::query()->findOrFail($id);
        $resource->delete();

        return ApiResponseHelper::sendSuccessResponse();
    }
}
