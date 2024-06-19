<?php

namespace App\Http\Controllers;

use App\DTO\PaymentCreateDTO;
use App\DTO\PaymentListDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\PaymentCreateRequest;
use App\Http\Requests\PaymentListRequest;
use App\Http\Resources\WorkSitePaymentListResource;
use App\Models\Payment;
use App\Models\WorkSite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkSitePaymentController extends Controller
{
    public function list(int $workSiteId, PaymentListRequest $request): JsonResponse
    {

        /**
         * @var array{
         *   date_from:string|null,
         *  date_to:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();

        $filteredData = PaymentListDTO::fromRequest($requestedData);
        $payments = Payment::query()->where(
            column: 'payable_id',
            operator: '=',
            value: $workSiteId
        )
            ->where(
                column: 'payable_type',
                operator: '=',
                value: WorkSite::class
            )
            ->when(
                value: $filteredData->dateFrom && $filteredData->dateTo,
                callback: function (Builder $builder) use ($filteredData) {
                    $builder->whereDate(
                        column: 'payment_date',
                        operator: '>=',
                        value: $filteredData->dateFrom
                    )
                        ->whereDate(
                            column: 'payment_date',
                            operator: '<=',
                            value: $filteredData->dateTo);
                })
            ->get();

        return ApiResponseHelper::sendSuccessResponse(
            result: new Result(WorkSitePaymentListResource::collection($payments)));
    }

    /**
     * Handle the incoming request.
     */
    public function create(PaymentCreateRequest $request, int $workSiteId): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        /**
         * @var array{
         *  payable_id : int|null,
         *  payable_type :string|null,
         *  payment_date :string,
         *  payment_amount :int,
         *  payment_type :int|null,
         * } $requestedData
         */
        $requestedData = $request->validated();
        $workSite->payments()->create(PaymentCreateDTO::fromRequest($requestedData)->toArray());

        return ApiResponseHelper::sendSuccessResponse();
    }
}
