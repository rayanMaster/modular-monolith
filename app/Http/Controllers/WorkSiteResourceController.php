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
    public function list(int $workSiteId): JsonResponse
    {
        $workSite = WorkSite::query()->with(['resources'])->findOrFail($workSiteId);

        return ApiResponseHelper::sendSuccessResponse(new Result(WorkSiteResourceListResource::collection($workSite->resources)));
    }

    public function add(int $workSiteId, int $resourceId, WorkSiteResourceAddRequest $request): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        $resource = Resource::query()->findOrFail($resourceId);

        /**
         * @var array{
         *   quantity:int,
         *   price:int
         * } $requestedData
         */
        $requestedData = $request->validated();
        $dataToAdd = WorkSiteResourceAddDTO::fromRequest($requestedData);
        $resourcesData = [
            $resource->id => [
                'quantity' => $dataToAdd->quantity,
                'price' => $dataToAdd->price,
            ],
        ];
        $workSite->resources()->syncWithoutDetaching($resourcesData);

        return ApiResponseHelper::sendSuccessResponse();
    }
}
