<?php

namespace App\Http\Controllers;

use App\DTO\WorkerCreateDTO;
use App\DTO\WorkerUpdateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkerCreateRequest;
use App\Http\Requests\WorkerUpdateRequest;
use App\Http\Resources\WorkerDetailsResource;
use App\Http\Resources\WorkerListResource;
use App\Mapper\WorkerCreateMapper;
use App\Mapper\WorkerUpdateMapper;
use App\Models\Worker;
use App\Repository\WorkerRepository;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function __construct(
        private readonly WorkerRepository $workerRepository
    )
    {

    }
    public function list(){
        $workers = Worker::query()->get();
        return ApiResponseHelper::sendResponse(new Result(WorkerListResource::collection($workers)));
    }

    public function store(WorkerCreateRequest $request){
        $toSave = WorkerCreateMapper::fromEloquent(WorkerCreateDTO::fromRequest($request->validated()));
        Worker::query()->create($toSave);
        return ApiResponseHelper::sendSuccessResponse(new Result());
    }

    /**
     * @throws \Throwable
     */
    public function update(WorkerUpdateRequest $request, int $workerId){
        $toUpdate = WorkerUpdateMapper::fromEloquent(WorkerUpdateDTO::fromRequest($request->validated()));
        $this->workerRepository->update($workerId, $toUpdate);
        return ApiResponseHelper::sendSuccessResponse(new Result());
    }

    public function show($id){
        $worker = Worker::query()->findOrFail($id);
        return ApiResponseHelper::sendResponse(new Result(WorkerDetailsResource::make($worker)));
    }

    public function destroy(int $workerId){
        $worker = Worker::query()->findOrFail($workerId);
        $worker->delete();
        return ApiResponseHelper::sendResponse(new Result());
    }
}
