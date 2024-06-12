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

class EmployeeController extends Controller
{
    public function __construct(
        private readonly WorkerRepository $workerRepository
    ) {

    }

    public function list()
    {
        $workers = Employee::query()->get();

        return ApiResponseHelper::sendResponse(new Result(EmployeeListResource::collection($workers)));
    }

    public function store(EmployeeCreateRequest $request)
    {
        $toSave = EmployeeCreateMapper::fromEloquent(WorkerCreateDTO::fromRequest($request->validated()));
        Employee::query()->create($toSave);

        return ApiResponseHelper::sendSuccessResponse(new Result());
    }

    /**
     * @throws \Throwable
     */
    public function update(EmployeeUpdateRequest $request, int $workerId)
    {
        $toUpdate = EmployeeUpdateMapper::fromEloquent(WorkerUpdateDTO::fromRequest($request->validated()));
        $this->workerRepository->update($workerId, $toUpdate);

        return ApiResponseHelper::sendSuccessResponse(new Result());
    }

    public function show($id)
    {
        $worker = Employee::query()->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(EmployeeDetailsResource::make($worker)));
    }

    public function destroy(int $workerId)
    {
        $worker = Employee::query()->findOrFail($workerId);
        $worker->delete();

        return ApiResponseHelper::sendResponse(new Result());
    }
}
