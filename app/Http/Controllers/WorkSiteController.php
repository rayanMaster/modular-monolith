<?php

namespace App\Http\Controllers;

use App\DTO\WorkSiteCreateDTO;
use App\DTO\WorkSiteUpdateDTO;
use App\Enums\WorkSiteCompletionStatusEnum;
use App\Exceptions\UnAbleToCloseWorkSiteException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\WorkSiteCreateRequest;
use App\Http\Requests\WorkSiteUpdateRequest;
use App\Http\Resources\WorkSiteDetailsResource;
use App\Http\Resources\WorkSiteListResource;
use App\Mapper\WorkSiteCreateMapper;
use App\Mapper\WorkSiteUpdateMapper;
use App\Models\Contractor;
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
                /**
                 * @var array{
                 *  title: string,
                 *  description: string,
                 *  customer_id?: int|null,
                 *  category_id?: int|null,
                 *  parent_work_site_id?: int|null,
                 *  contractor_id?: int|null,
                 *  starting_budget?: float|null,
                 *  cost?: float|null,
                 *  address_id?: int|null,
                 *  workers_count?: int|null,
                 *  receipt_date?: string|null,
                 *  starting_date?: string|null,
                 *  deliver_date?: string|null,
                 *  reception_status?: int|null,
                 *  completion_status?: int|null,
                 *  resources?: array{
                 *       id:int,
                 *       quantity:int,
                 *       price:float
                 *   }|null,
                 *  payments?: array{
                 *       payment_amount:float,
                 *       payment_date: string
                 *   }|null,
                 *  images?: string|null
                 *  } $requestedData
                 */
                $requestedData = $request->validated();
                $data = WorkSiteCreateDTO::fromRequest($requestedData);

                $workSiteData = WorkSiteCreateMapper::toWorkSiteEloquent($data);

                $workSite = WorkSite::query()->create($workSiteData);

                $resourcesData = WorkSiteCreateMapper::toWorkSiteResourcesEloquent($data);
                $workSite->resources()->syncWithoutDetaching($resourcesData);

                $paymentData = WorkSiteCreateMapper::toPaymentEloquent($data);

                foreach ($paymentData as $payment) {
                    $workSite->payments()->create($payment);
                }

                $files = $request->file('images');
                if (isset($files) && is_array($files)) {
                    foreach ($files as $file) {
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
    public function show(int $id): JsonResponse
    {
        $worksite = WorkSite::query()->with(['customer', 'payments', 'resources'])->findOrFail($id);

        return ApiResponseHelper::sendResponse(new Result(WorkSiteDetailsResource::make($worksite)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws \Throwable
     */
    public function update(WorkSiteUpdateRequest $request, int $id): JsonResponse
    {

        DB::transaction(
            callback: function () use ($request, $id) {
                /**
                 * @var array{
                 *  title: string |null,
                 *  description: string|null,
                 *  customer_id?: int|null,
                 *  category_id?: int|null,
                 *  parent_work_site_id?: int|null,
                 *  contractor_id?: float|null,
                 *  starting_budget?: float|null,
                 *  cost?: float|null,
                 *  address_id?: int|null,
                 *  workers_count?: int|null,
                 *  receipt_date?: string|null,
                 *  starting_date?: string|null,
                 *  deliver_date?: string|null,
                 *  reception_status?: int|null,
                 *  completion_status?: int|null,
                 *  resources?: array{
                 *    id:int,
                 *    quantity:int,
                 *    price:float
                 *  }|null,
                 *  payments?: array{
                 *    payment_amount:float,
                 *    payment_date: string
                 *  }|null,
                 *  image?: string|null
                 *  } $requestedData
                 */
                $requestedData = $request->validated();
                $workSite = WorkSite::query()->findOrFail($id);

                $data = WorkSiteUpdateDTO::fromRequest($requestedData);

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
    public function close(int $id): JsonResponse
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
            throw new UnAbleToCloseWorkSiteException("You can't close a workSite with active sub-worksites");
        }

        if ($workSitePayments < $workSite->cost) {
            throw new UnAbleToCloseWorkSiteException("You can't close a workSite with unpaid payment");
        }

        $workSite->update([
            'completion_status' => WorkSiteCompletionStatusEnum::CLOSED,
        ]);

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws \Throwable
     */
    public function assignContractor(int $workSiteId, int $contractorId): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        Contractor::query()->findOrFail($contractorId);
        $this->workSiteRepository->update($workSite->id, [
            'contractor_id' => $contractorId,
        ], true);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function unAssignContractor(int $workSiteId, int $contractorId): JsonResponse
    {
        $workSite = WorkSite::query()->findOrFail($workSiteId);
        Contractor::query()->findOrFail($contractorId);

        $this->workSiteRepository->update($workSite->id, [
            'contractor_id' => null,
        ], true);

        return ApiResponseHelper::sendSuccessResponse();
    }
}
