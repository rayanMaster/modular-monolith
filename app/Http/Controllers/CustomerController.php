<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerDetailsResource;
use App\Http\Resources\CustomerListResource;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function list(): \Illuminate\Http\JsonResponse
    {
        $customers = Customer::query()->get();

        return ApiResponseHelper::sendResponse(new Result(CustomerListResource::collection($customers)));
    }

    public function store(CustomerCreateRequest $request)
    {
        Customer::query()->create($request->validated());

    }

    public function show($id)
    {
        $customer = Customer::query()->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(CustomerDetailsResource::make($customer)));
    }

    public function update(CustomerUpdateRequest $request, $id)
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->update($request->validated());
    }

    public function destroy($id)
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->delete();

    }
}
