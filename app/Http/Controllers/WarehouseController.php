<?php

namespace App\Http\Controllers;

use App\DTO\WarehouseCreateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\ErrorResult;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WarehouseCreateRequest;
use App\Http\Requests\WarehouseItemsAddRequest;
use App\Http\Requests\WarehouseItemsMoveItemsRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Http\Resources\WarehouseDetailsResource;
use App\Http\Resources\WarehouseListResource;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class WarehouseController extends Controller
{


    public function list(): JsonResponse
    {
        $warehouses = Warehouse::query()->get();
        return ApiResponseHelper::sendSuccessResponse(new Result(WarehouseListResource::collection($warehouses)));
    }

    public function store(WarehouseCreateRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     name:string,
         *     address_id:int|null
         * } $requestedData
         */
        $requestedData = $request->validated();
        Warehouse::query()->create($requestedData);
        return ApiResponseHelper::sendSuccessResponse();

    }

    public function update(int $warehouseId, WarehouseUpdateRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     name:string|null,
         *     address_id:int|null
         * } $requestedData
         */
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        $requestedData = $request->validated();
        $warehouse->update($requestedData);

        return ApiResponseHelper::sendSuccessResponse();

    }

    public function show(int $warehouseId): JsonResponse
    {
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        return ApiResponseHelper::sendSuccessResponse(new Result(WarehouseDetailsResource::make($warehouse)));
    }

    public function destroy(int $warehouseId): JsonResponse
    {
        Warehouse::query()->findOrFail($warehouseId)->delete();
        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws Throwable
     */
    public function addItems(int $warehouseId, WarehouseItemsAddRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     items: array{
         *      item_id:int,
         *      quantity:float,
         *      price:float
         *     },
         *     supplier_id:int|null,
         *     date:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();

        $dataToSave = array_map(function ($item) use ($requestedData, $warehouseId) {
            return [
                'warehouse_id' => $warehouseId,
                'supplier_id' => $requestedData['supplier_id'],
                'date' => $requestedData['date'],
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }, $requestedData['items']);
        try {
            DB::transaction(
                callback: function () use ($dataToSave) {
                    WarehouseItem::query()->insert($dataToSave);
                },
                attempts: 3);
        } catch (QueryException $exception) {
            if ($exception->getCode() == 23000) {
                return ApiResponseHelper::sendErrorResponse(new ErrorResult("Item already exists in this warehouse", Response::HTTP_CONFLICT));
            }
        }
        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws Throwable
     */
    public function moveItems(int $fromWarehouseId, WarehouseItemsMoveItemsRequest $request)
    {
        /**
         * @var array{
         *     items: array{
         *      item_id:int,
         *      quantity:float,
         *      to_warehouse_id:int
         *     },
         * } $requestedData
         */
        $requestedData = $request->validated();

        /**
         * @var array<int,array{
         *     from_warehouse_id : int,
         *     to_warehouse_id : int,
         *     item_id:int,
         *     quantity:int
         * }> $dataToMove
         */
        $dataToMove = array_map(function ($item) use ($requestedData, $fromWarehouseId) {
            return [
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $item['to_warehouse_id'],
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
            ];
        }, $requestedData['items']);
        DB::transaction(
            callback: function () use ($dataToMove) {
                foreach ($dataToMove as $item) {
                    $currentItem = WarehouseItem::query()->findOrFail($item['item_id']);
                    if($currentItem->q)
                    WarehouseItem::query()->where('warehouse_id', $item['from_warehouse_id'])
                        ->where('item_id', $item['item_id'])
                        ->decrement('quantity', $item['quantity']);

                    WarehouseItem::query()->where('warehouse_id', $item['to_warehouse_id'])
                        ->where('item_id', $item['item_id'])
                        ->increment('quantity', $item['quantity']);

                }
            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse();
    }

}
