<?php

namespace App\Http\Controllers;

use App\Enums\ChartOfAccountNamesEnum;
use App\Exceptions\CustomerNotRelatedToWorksiteException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Helpers\CacheHelper;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncDTO;
use App\Http\Requests\PaymentListRequest;
use App\Http\Requests\WorksitePaymentCreateRequest;
use App\Http\Resources\WorksitePaymentListResource;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Worksite;
use App\Services\PaymentSyncService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class WorksitePaymentController extends Controller
{
    public function __construct(
        private readonly PaymentSyncService $paymentSyncService
    ) {}

    public function list(int $workSiteId, PaymentListRequest $request): JsonResponse
    {

        /**
         * @var array{
         *   date_from:string|null,
         *  date_to:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();

        $payments = Payment::query()->where(
            column: 'payable_id',
            operator: '=',
            value: $workSiteId
        )
            ->where(
                column: 'payable_type',
                operator: '=',
                value: Worksite::class
            )
            ->when(
                value: $requestedData['date_from'] &&
                $requestedData['date_to'],
                callback: function (Builder $builder) use ($requestedData) {
                    $builder->whereDate(
                        column: 'payment_date',
                        operator: '>=',
                        value: $requestedData['date_from'] ?? null
                    )
                        ->whereDate(
                            column: 'payment_date',
                            operator: '<=',
                            value: $requestedData['date_to'] ?? null);
                })
            ->get();

        return ApiResponseHelper::sendSuccessResponse(
            result: new Result(WorksitePaymentListResource::collection($payments)));
    }

    /**
     * Handle the incoming request.
     *
     * @throws Throwable
     */
    public function create(WorksitePaymentCreateRequest $request, int $workSiteId): JsonResponse
    {
        $worksite = Worksite::query()->findOrFail($workSiteId);
        /**
         * @var array{
         *  payment_from_id : int,
         *  payment_from_type :string,
         *  payment_date :string,
         *  payment_amount :float,
         *  payment_type :int|null,
         * } $requestedData
         */
        $requestedData = $request->validated();
        $customer = Customer::query()->findOrFail(id: $requestedData['payment_from_id']);

        if ($worksite->customer_id !== $customer->id) {
            throw new CustomerNotRelatedToWorksiteException(message: 'Customer not related to this worksite');
        }

        DB::transaction(function () use ($worksite, $requestedData, $customer) {
            if ($requestedData['payment_from_type'] == ChartOfAccountNamesEnum::CLIENTS->value) {
                $makePaymentDTO = new PaymentSyncDTO(
                    customer_uuid: $customer?->uuid,
                    worksite_uuid: $worksite->uuid,
                    payment_date: $requestedData['payment_date'],
                    payment_amount: $requestedData['payment_amount']);
                $this->paymentSyncService->syncPaymentsToAccounting($makePaymentDTO);
                $cache = new CacheHelper;
                $cache->flushCache('worksite_payments', $worksite->uuid);
            }
        });

        return ApiResponseHelper::sendSuccessResponse();
    }
}
