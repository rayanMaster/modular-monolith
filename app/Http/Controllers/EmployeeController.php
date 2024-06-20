<?php

namespace App\Http\Controllers;

use App\DTO\WorkerCreateDTO;
use App\DTO\WorkerUpdateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeDetailsResource;
use App\Http\Resources\EmployeeListResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{


    public function list(): JsonResponse
    {
        $workers = User::query()->get();

        return ApiResponseHelper::sendSuccessResponse(new Result(EmployeeListResource::collection($workers)));
    }

    public function store(EmployeeCreateRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     first_name:string,
         *     last_name:string|null,
         *     phone:string,
         *     password:string,
         * } $requestedData
         */
        $requestedData = $request->validated();
        $toSave = WorkerCreateDTO::fromRequest($requestedData);
        User::query()->create([
            'first_name' => $toSave->firstName,
            'last_name' => $toSave->lastName,
            'phone' => $toSave->phone,
            'password' => $toSave->password,
        ]);

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws \Throwable
     */
    public function update(EmployeeUpdateRequest $request, int $workerId): JsonResponse
    {
        /**
         * @var array{
         *     first_name:string|null,
         *     last_name:string|null,
         *     phone:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();
        $worker = User::query()->findOrFail($workerId);
        $toUpdate = WorkerUpdateDTO::fromRequest($requestedData);
        $worker->update([
            'first_name' => $toUpdate->firstName,
        ]);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function show(int $workerId): JsonResponse
    {
        $worker = User::query()->findOrFail($workerId);

        return ApiResponseHelper::sendSuccessResponse(new Result(EmployeeDetailsResource::make($worker)));
    }

    public function destroy(int $workerId): JsonResponse
    {
        $worker = User::query()->findOrFail($workerId);
        $worker->delete();

        return ApiResponseHelper::sendSuccessResponse();
    }
}
