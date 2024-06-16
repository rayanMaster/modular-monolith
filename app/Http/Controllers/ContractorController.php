<?php

namespace App\Http\Controllers;

use App\DTO\ContractorCreateDTO;
use App\DTO\ContractorUpdateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\ContractorCreateRequest;
use App\Http\Requests\ContractorUpdateRequest;
use App\Http\Resources\ContractorDetailsResource;
use App\Http\Resources\ContractorListResource;
use App\Mapper\ContractorCreateMapper;
use App\Mapper\ContractorUpdateMapper;
use App\Models\Contractor;
use App\Repository\ContractorRepository;
use Illuminate\Http\JsonResponse;

class ContractorController extends Controller
{
    public function __construct(
        private readonly ContractorRepository $contractorRepository
    ) {
    }

    public function list(): JsonResponse
    {
        $result = $this->contractorRepository->list();

        return ApiResponseHelper::sendResponse(new Result(ContractorListResource::collection($result)));
    }

    /**
     * @throws \Throwable
     */
    public function store(ContractorCreateRequest $request): JsonResponse
    {
        $dataFromRequest = ContractorCreateDTO::fromRequest($request->validated());
        $this->contractorRepository->create(ContractorCreateMapper::toEloquent($dataFromRequest));

        return ApiResponseHelper::sendSuccessResponse();

    }

    /**
     * @throws \Throwable
     */
    public function update(ContractorUpdateRequest $request, int $id): JsonResponse
    {
        $contractor = Contractor::query()->findOrFail($id);
        $dataFromRequest = ContractorUpdateDTO::fromRequest($request->validated());
        $this->contractorRepository->update($contractor->id, ContractorUpdateMapper::toEloquent($dataFromRequest));

        return ApiResponseHelper::sendSuccessResponse();

    }

    public function show(int $id): JsonResponse
    {
        $contractor = Contractor::query()->findOrFail($id);

        return ApiResponseHelper::sendSuccessResponse(new Result(ContractorDetailsResource::make($contractor)));
    }
}
