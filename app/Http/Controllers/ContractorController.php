<?php

namespace App\Http\Controllers;

use App\DTO\ContractorCreateDTO;
use App\DTO\ContractorUpdateDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\ContractorCreateRequest;
use App\Http\Requests\ContractorUpdateRequest;
use App\Mapper\ContractorCreateMapper;
use App\Mapper\ContractorUpdateMapper;
use App\Models\Contractor;
use App\Repository\ContractorRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    public function __construct(
        private readonly ContractorRepository $contractorRepository
    )
    {

    }

    /**
     * @throws \Throwable
     */
    public function store(ContractorCreateRequest $request): JsonResponse
    {
        $dataFromRequest = ContractorCreateDTO::fromRequest($request->validated());
        $this->contractorRepository->create(ContractorCreateMapper::toEloquent($dataFromRequest));

        return ApiResponseHelper::sendSuccessResponse(new Result());

    }

    /**
     * @throws \Throwable
     */
    public function update(ContractorUpdateRequest $request, int $id): JsonResponse
    {
        $contractor = Contractor::query()->findOrFail($id);
        $dataFromRequest = ContractorUpdateDTO::fromRequest($request->validated());
        $this->contractorRepository->update($id,ContractorUpdateMapper::toEloquent($dataFromRequest));

        return ApiResponseHelper::sendSuccessResponse(new Result());

    }
}
