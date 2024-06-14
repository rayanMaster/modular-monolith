<?php

namespace App\Http\Controllers;

use App\DTO\WorkSiteResourceAddDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteResourceAddRequest;
use App\Http\Resources\WorkSiteResourceListResource;
use App\Models\Resource;
use App\Models\WorkSite;
use Illuminate\Http\JsonResponse;

class WorkSiteResourceController extends Controller
{
    public function list(int $workSiteId)
    {
        $workSite = WorkSite::query()->with(['resources'])->findOrFail($workSiteId);

        return ApiResponseHelper::sendResponse(new Result(WorkSiteResourceListResource::collection($workSite->resources)));
    }

    public function add(int $workSiteId, int $resourceId, WorkSiteResourceAddRequest $request): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        $resource = Resource::query()->findOrFail($resourceId);

        $dataToAdd = WorkSiteResourceAddDTO::fromRequest($request->validated());
        $resourcesData = [
            $resource->id => [
                'quantity' => $dataToAdd->quantity,
                'price' => $dataToAdd->price,
            ],
        ];
        $workSite->resources()->syncWithoutDetaching($resourcesData);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update($request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
