<?php

namespace App\Http\Controllers;

use App\Enums\WarehouseItemThresholdsEnum;
use App\Exceptions\InValidWarehouseItemMoveException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorksiteItemAddRequest;
use App\Http\Resources\WorksiteItemListResource;
use App\Models\Item;
use App\Models\WarehouseItem;
use App\Models\Worksite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class WorksiteItemController extends Controller
{
    public function list(int $workSiteId): JsonResponse
    {
        $workSite = Worksite::query()->with(['items.warehouse'])->findOrFail($workSiteId);

        $workSite->items->map(function (Item $item) {
            $item->quantityInWarehouse = $item->warehouse?->quantity;
            $item->inStock = $item->warehouse?->quantity > WarehouseItemThresholdsEnum::LOW->value ?
                'In-Stock' :
                ($item->warehouse?->quantity > 0 ? 'Low-Stock' : 'Off-Stock');
        });

        return ApiResponseHelper::sendSuccessResponse(new Result(WorksiteItemListResource::collection($workSite->items)));
    }

    /**
     * @throws \Throwable
     */
    public function addItems(int $workSiteId, WorksiteItemAddRequest $request): JsonResponse
    {
        $workSite = Worksite::query()->findOrFail($workSiteId);

        /**
         * @var array{
         *   warehouse_id:int,
         *   items:array<string,array{
         *     item_id:int,
         *     quantity:float,
         *     price:float
         *   }>
         * } $requestedData
         */
        $requestedData = $request->validated();
        DB::transaction(
            callback: function () use ($workSite, $requestedData) {
                foreach ($requestedData['items'] as $item) {
                    $currentItemQtyInWarehouse = WarehouseItem::query()
                        ->where('warehouse_id', $requestedData['warehouse_id'])
                        ->where('item_id', $item['item_id'])
                        ->first()
                        ?->quantity;
                    if ($currentItemQtyInWarehouse < $item['quantity']) {
                        throw new InValidWarehouseItemMoveException('Not enough items in warehouse', Response::HTTP_BAD_REQUEST);
                    }
                    $itemsData = [
                        $item['item_id'] => [
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ],
                    ];
                    $workSite->items()->syncWithoutDetaching($itemsData);

                    WarehouseItem::query()->where('warehouse_id', $requestedData['warehouse_id'])
                        ->where('item_id', $item['item_id'])
                        ->decrement('quantity', $item['quantity']);
                }
            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse();
    }
}
