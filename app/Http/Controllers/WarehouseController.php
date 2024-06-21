<?php

namespace App\Http\Controllers;

use App\DTO\WarehouseCreateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WarehouseCreateRequest;
use App\Http\Requests\WarehouseItemsAddRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Http\Resources\WarehouseDetailsResource;
use App\Http\Resources\WarehouseListResource;
use App\Models\WareHouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function show(int $warehouseId): JsonResponse{
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        return ApiResponseHelper::sendSuccessResponse(new Result(WarehouseDetailsResource::make($warehouse)));
    }

    public function destroy(int $warehouseId): JsonResponse{
        Warehouse::query()->findOrFail($warehouseId)->delete();
        return ApiResponseHelper::sendSuccessResponse();
    }

    public function addItems(int $warehouseId, WarehouseItemsAddRequest $request)
    {

    }

}
