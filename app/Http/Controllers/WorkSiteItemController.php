<?php

namespace App\Http\Controllers;

use App\DTO\WorkSiteResourceAddDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteItemAddRequest;
use App\Http\Resources\WorkSiteItemListResource;
use App\Models\Item;
use App\Models\WorkSite;
use Illuminate\Http\JsonResponse;

class WorkSiteItemController extends Controller
{
    public function list(int $workSiteId): JsonResponse
    {
        $workSite = WorkSite::query()->with(['items'])->findOrFail($workSiteId);

        return ApiResponseHelper::sendSuccessResponse(new Result(WorkSiteItemListResource::collection($workSite->items)));
    }

    public function add(int $workSiteId, int $resourceId, WorkSiteItemAddRequest $request): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        $resource = Item::query()->findOrFail($resourceId);

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
        $workSite->items()->syncWithoutDetaching($resourcesData);

        return ApiResponseHelper::sendSuccessResponse();
    }
}
