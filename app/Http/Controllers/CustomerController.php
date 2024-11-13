<?php

namespace App\Http\Controllers;

use App\Enums\WorksiteCompletionStatusEnum;
use App\Exceptions\UnAbleToDeleteCustomerException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\CustomerSync\CustomerSyncDTO;
use App\Http\Integrations\Accounting\Requests\CustomerSync\CustomerSyncRequest;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerDetailsResource;
use App\Http\Resources\CustomerListResource;
use App\Models\Customer;
use App\Services\CustomerSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Str;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerSyncService $customerSyncService
    )
    {

    }
    public function list(): JsonResponse
    {
        $customers = Customer::query()->get();

        return ApiResponseHelper::sendSuccessResponse(new Result(CustomerListResource::collection($customers)));
    }

    /**
     * @throws RequestException
     */
    public function store(CustomerCreateRequest $request): JsonResponse
    {
        /** @var array{
         * first_name: string,
         * last_name: string|null,
         * } $requestData
         */
        $requestData = $request->validated();
        $filteredData = array_filter([
            'uuid' => Str::uuid()->toString(),
            'first_name' => $requestData['first_name'],
            'last_name' => $requestData['last_name'] ?? null,
        ], fn($value) => $value != null);

        $customer = Customer::query()->create($filteredData);
        try {
            $connector = new AccountingConnector();
            $customerSyncDTO = new CustomerSyncDTO(
                uuid: $customer->uuid,
                name: $customer->fullName
            );
            $request = new CustomerSyncRequest($customerSyncDTO);

            $connector->send($request);
//            $this->customerSyncService->syncCustomerToAccounting($customerSyncDTO);

        } catch (FatalRequestException $exception) {
            Log::info("customer sync", [$exception->getMessage()]);
        }

        return ApiResponseHelper::sendSuccessResponse();

    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::query()->with(['address', 'payments'])->findOrFail($id);

        return ApiResponseHelper::sendSuccessResponse(new Result(CustomerDetailsResource::make($customer)));
    }



    public function update(CustomerUpdateRequest $request, int $id): void
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->update($request->validated());
    }

    /**
     * @throws UnAbleToDeleteCustomerException
     */
    public function destroy(int $id): void
    {

        $customer = Customer::query()->findOrFail($id);

        $relatedWorksite = $customer->whereHas('worksite', function ($query) {
            $query->where('completion_status', '<>', WorksiteCompletionStatusEnum::CLOSED);
        })->exists();
        if ($relatedWorksite) {
            throw new UnAbleToDeleteCustomerException('Unable to delete customer with a not closed work site');
        }
        $customer->delete();

    }
}
