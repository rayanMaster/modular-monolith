<?php

namespace App\Http\Controllers;

use App\Enums\OrderPriorityEnum;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Models\Order;
use App\Models\OrderItem;
use Auth;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(OrderCreateRequest $request): JsonResponse
    {

        /**
         * @var array{
         *     work_site_id: int,
         *     items:array<string,array{
         *     item_id:int,
         *     quantity:int
         *     }>,
         *     priority:int|null
         * } $requestedData
         */
        $requestedData = $request->validated();

        $order = Order::query()->create([
            'work_site_id' => $requestedData['work_site_id'],
            'priority' => $requestedData['priority'] ?? OrderPriorityEnum::NORMAL->value,
            'created_by' => Auth::id(),
        ]);
        $orderItemsData = array_map(function ($item) use ($order) {
            return [
                'order_id' => $order->id,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],

            ];
        }, $requestedData['items']);
        OrderItem::query()->insert($orderItemsData);

        return ApiResponseHelper::sendSuccessResponse(new Result(OrderDetailsResource::make($order)));

    }
}
