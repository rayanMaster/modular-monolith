<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerDetailsResource;
use App\Http\Resources\CustomerListResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function list(): JsonResponse
    {
        $customers = Customer::query()->get();

        return ApiResponseHelper::sendResponse(new Result(CustomerListResource::collection($customers)));
    }

    public function store(CustomerCreateRequest $request): JsonResponse
    {
        Customer::query()->create($request->validated());

        return ApiResponseHelper::sendSuccessResponse();

    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::query()->with(['address', 'payments'])->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(CustomerDetailsResource::make($customer)));
    }

    public function update(CustomerUpdateRequest $request, int $id): void
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->update($request->validated());
    }

    public function destroy(int $id): void
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->delete();

    }
}
