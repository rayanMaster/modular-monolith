<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\ResourceCreateRequest;
use App\Http\Requests\ResourceUpdateRequest;
use App\Http\Resources\ResourceDetailsResource;
use App\Http\Resources\ResourceListResource;
use App\Models\Resource;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $resources = Resource::query()->get();

        return ApiResponseHelper::sendResponse(new Result(ResourceListResource::collection($resources)));
    }

    public function store(ResourceCreateRequest $request)
    {
        Resource::query()->create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = Resource::query()->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(ResourceDetailsResource::make($resource)));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResourceUpdateRequest $request, string $id)
    {
        $resource = Resource::query()->findOrFail($id);
        $resource->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resource = Resource::query()->findOrFail($id);
        $resource->delete();
    }
}
