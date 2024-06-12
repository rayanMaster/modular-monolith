<?php

namespace App\Http\Controllers;

use App\DTO\WorkSiteContractorAssignDTO;
use App\DTO\WorkSiteCreateDTO;
use App\DTO\WorkSiteUpdateDTO;
use App\Enums\ConfirmEnum;
use App\Enums\WorkSiteCompletionStatusEnum;
use App\Exceptions\UnAbleToCloseWorkSiteException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteContractorAssignRequest;
use App\Http\Requests\WorkSiteCreateRequest;
use App\Http\Requests\WorkSiteUpdateRequest;
use App\Http\Resources\WorkSiteDetailsResource;
use App\Http\Resources\WorkSiteListResource;
use App\Mapper\WorkSiteCreateMapper;
use App\Mapper\WorkSiteUpdateMapper;
use App\Models\WorkSite;
use App\Repository\WorkSiteRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;

class WorkSiteController extends Controller
{
    public function __construct(
        //            private readonly IFileManager $fileManager
        private readonly WorkSiteRepository $workSiteRepository,
    ) {
    }

    public function list(): JsonResponse
    {
        $workSites = WorkSite::query()->with(['payments', 'address'])->get();

        return ApiResponseHelper::sendResponse(new Result(WorkSiteListResource::collection($workSites)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws \Throwable
     */
    public function store(WorkSiteCreateRequest $request): JsonResponse
    {
        DB::transaction(
            callback: function () use ($request) {
                $data = WorkSiteCreateDTO::fromRequest($request->validated());

                $workSiteData = WorkSiteCreateMapper::toWorkSiteEloquent($data);

                $workSite = WorkSite::query()->create($workSiteData);

                $resourcesData = WorkSiteCreateMapper::toWorkSiteResourcesEloquent($data);
                $workSite->resources()->syncWithoutDetaching($resourcesData);

                $paymentData = WorkSiteCreateMapper::toPaymentEloquent($data);

                foreach ($paymentData as $payment) {
                    $workSite->payments()->create($payment);
                }

                $file = $request->file('image');
                if ($file) {
                    $fileNameParts = explode('.', $file->getClientOriginalName());
                    $fileName = $fileNameParts[0];
                    $path = lcfirst('WorkSite');
                    $name = $fileName.'_'.now()->format('YmdH');

                    if (! File::exists(public_path('storage/'.$path))) {
                        File::makeDirectory(public_path('storage/'.$path));
                    }

                    $fullPath = public_path('storage/'.$path).'/'.$name.'.webp';

                    // create new manager instance with desired driver
                    $manager = new \Intervention\Image\ImageManager(new Driver());

                    // read image from filesystem
                    $image = $manager->read($file)->save($fullPath);
                }
                //        $this->fileManager->upload($files);
            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse(
            new Result());
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $worksite = WorkSite::query()->with(['customer', 'payments', 'resources'])->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(WorkSiteDetailsResource::make($worksite)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws \Throwable
     */
    public function update(WorkSiteUpdateRequest $request, $id)
    {
        DB::transaction(
            callback: function () use ($request, $id) {

                $workSite = WorkSite::query()->findOrFail($id);

                $data = WorkSiteUpdateDTO::fromRequest($request->validated());

                $workSiteData = WorkSiteUpdateMapper::toWorkSiteEloquent($data);
                $this->workSiteRepository->update($workSite->id, $workSiteData);

            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse(
            new Result());
    }

    /**
     * @throws UnAbleToCloseWorkSiteException
     */
    public function close(int $id)
    {
        $workSite = WorkSite::query()->with(['subWorksites'])->findOrFail($id);
        $relatedActiveSubWorkSitesCount = $workSite->whereHas('subWorksites', function (Builder $query) {
            return $query->where(
                column: 'completion_status',
                operator: '<>',
                value: WorkSiteCompletionStatusEnum::CLOSED
            );
        })->count();

        $workSitePayments = $workSite->payments->sum('amount');

        if ($relatedActiveSubWorkSitesCount > 0) {
            throw new UnAbleToCloseWorkSiteException("You can't close a worksite with active sub-worksites");
        }

        if ($workSitePayments < $workSite->cost) {
            throw new UnAbleToCloseWorkSiteException("You can't close a worksite with unpaid payment");
        }

        $workSite->update([
            'completion_status' => WorkSiteCompletionStatusEnum::CLOSED,
        ]);

        return ApiResponseHelper::sendSuccessResponse(
            new Result());
    }

    public function assignEmployee()
    {

    }

    /**
     * @throws \Throwable
     */
    public function assignContractor(int $workSiteId, WorkSiteContractorAssignRequest $request): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        $dataFromRequest = WorkSiteContractorAssignDTO::fromRequest($request->validated());
        $contractorId = $dataFromRequest->shouldRemove == ConfirmEnum::YES->value ? null : $dataFromRequest->contractorId;
        $this->workSiteRepository->update($workSite->id, [
            'contractor_id' => $contractorId,
        ], true);

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
