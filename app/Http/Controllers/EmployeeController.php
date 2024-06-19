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
use App\Mapper\EmployeeCreateMapper;
use App\Mapper\EmployeeUpdateMapper;
use App\Models\Employee;
use App\Repository\WorkerRepository;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly WorkerRepository $workerRepository
    ) {

    }

    public function list(): JsonResponse
    {
        $workers = Employee::query()->get();

        return ApiResponseHelper::sendSuccessResponse(new Result(EmployeeListResource::collection($workers)));
    }

    public function store(EmployeeCreateRequest $request): JsonResponse
    {
        /**
         * @var array{
         *     first_name:string
         * } $requestedData
         */
        $requestedData = $request->validated();
        $toSave = EmployeeCreateMapper::fromEloquent(WorkerCreateDTO::fromRequest($requestedData));
        Employee::query()->create($toSave);

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws \Throwable
     */
    public function update(EmployeeUpdateRequest $request, int $workerId): JsonResponse
    {
        /**
         * @var array{
         *     first_name:string|null
         * } $requestedData
         */
        $requestedData = $request->validated();
        $toUpdate = EmployeeUpdateMapper::fromEloquent(WorkerUpdateDTO::fromRequest($requestedData));
        $this->workerRepository->update($workerId, $toUpdate);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function show(int $workerId): JsonResponse
    {
        $worker = Employee::query()->findOrFail($workerId);

        return ApiResponseHelper::sendSuccessResponse(new Result(EmployeeDetailsResource::make($worker)));
    }

    public function destroy(int $workerId): JsonResponse
    {
        $worker = Employee::query()->findOrFail($workerId);
        $worker->delete();

        return ApiResponseHelper::sendSuccessResponse();
    }
}
