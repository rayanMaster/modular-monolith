<?php

namespace App\Http\Controllers;

use App\Enums\GeneralSettingNumericEnum;
use App\Enums\PaymentTypesEnum;
use App\Enums\WarehouseItemThresholdsEnum;
use App\Enums\WorksiteCompletionStatusEnum;
use App\Enums\WorksiteReceptionStatusEnum;
use App\Events\PaymentCreatedEvent;
use App\Exceptions\UnAbleToCloseWorksiteException;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Helpers\CacheHelper;
use App\Http\Integrations\Accounting\Requests\WorksiteSync\WorksiteSyncDTO;
use App\Http\Requests\WorksiteCreateRequest;
use App\Http\Requests\WorksiteUpdateRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\WorksiteDetailsResource;
use App\Http\Resources\WorksiteListResource;
use App\Models\Address;
use App\Models\Contractor;
use App\Models\Item;
use App\Models\Worksite;
use App\Services\PaymentSyncService;
use App\Services\WorksiteSyncService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Str;
use Throwable;

class WorksiteController extends Controller
{
    public function __construct(
        private readonly WorksiteSyncService $worksiteSyncService,
        private readonly PaymentSyncService  $paymentSyncService
    )
    {
    }

    public function list(): JsonResponse
    {
        $workSites = Worksite::query()
            ->with(['payments', 'address', 'pendingOrders'])
            ->orderBy('created_at', 'DESC')
            ->paginate(GeneralSettingNumericEnum::PER_PAGE->value);

        foreach ($workSites as $workSite) {
            $payments = $this->paymentSyncService->getPaymentsForWorksite($workSite);
            $workSite->customerPayments = $payments;
        }
        $pagination = PaginationResource::make($workSites);


        return ApiResponseHelper::sendSuccessResponse(new Result(
            result: WorksiteListResource::collection($workSites),
            paginate: $pagination
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws Throwable
     */
    public function store(WorkSiteCreateRequest $request): JsonResponse
    {
        DB::transaction(
            callback: function () use ($request) {
                /**
                 * @var array{
                 *  title: string,
                 *  description: string,
                 *  manager_id: int,
                 *  customer_id?: int|null,
                 *  category_id?: int|null,
                 *  parent_worksite_id?: int|null,
                 *  contractor_id?: int|null,
                 *  starting_budget?: float|null,
                 *  cost?: float|null,
                 *  address?: string|null,
                 *  city_id?: int|null,
                 *  workers_count?: int|null,
                 *  receipt_date?: string|null,
                 *  starting_date?: string|null,
                 *  deliver_date?: string|null,
                 *  reception_status?: int|null,
                 *  completion_status?: int|null,
                 *  items?: array<int,array{
                 *       id:int,
                 *       quantity:int,
                 *       price:float
                 *   }>|null,
                 *  payments?: array<int,array{
                 *       payment_amount:float,
                 *       payment_date: string
                 *   }>|null,
                 *  images?: string|null
                 *  } $requestedData
                 */
                $requestedData = $request->validated();

                // create address
                $address = null;
                if (array_key_exists('city_id', $requestedData)) {
                    $address = Address::query()->create([
                        'city_id' => $requestedData['city_id'],
                        'title' => $requestedData['address'] ?? null,
                    ]);

                }

                $dataToSave = array_filter([
                    'uuid' => Str::uuid()->toString(),
                    'title' => $requestedData['title'],
                    'description' => $requestedData['description'],
                    'manager_id' => $requestedData['manager_id'],
                    'customer_id' => $requestedData['customer_id'] ?? null,
                    'category_id' => $requestedData['category_id'] ?? null,
                    'contractor_id' => $requestedData['contractor_id'] ?? null,
                    'parent_worksite_id' => $requestedData['parent_worksite_id'] ?? null,
                    'starting_budget' => $requestedData['starting_budget'] ?? null,
                    'cost' => $requestedData['cost'] ?? null,
                    'address_id' => $address?->id,
                    'workers_count' => $requestedData['workers_count'] ?? null,
                    'receipt_date' => $requestedData['receipt_date'] ?? null,
                    'starting_date' => $requestedData['starting_date'] ?? null,
                    'deliver_date' => $requestedData['deliver_date'] ?? null,
                    'reception_status' => $requestedData['reception_status'] ?? WorkSiteReceptionStatusEnum::SCRATCH->value,
                    'completion_status' => $requestedData['completion_status'] ?? WorkSiteCompletionStatusEnum::PENDING->value,
                ], fn($value) => $value != null);

                $workSite = Worksite::query()->create($dataToSave);
                try {
                    $this->worksiteSyncService->syncWorksiteToAccounting(new WorksiteSyncDTO($workSite->uuid, $workSite->title));

                } catch (FatalRequestException $exception) {
                    Log::info('problem', [$exception->getMessage()]);
                }

                $resourcesData = [];
                if (array_key_exists('items', $requestedData) &&
                    is_array($requestedData['items']) && count($requestedData['items']) > 0) {
                    foreach ($requestedData['items'] as $resource) {
                        if (is_array($resource)) {
                            $item = [
                                'quantity' => $resource['quantity'],
                                'price' => $resource['price'],
                            ];
                            $resourcesData[$resource['id']] = $item;
                        }
                    }
                }
                $workSite->items()->syncWithoutDetaching($resourcesData);
                // prepare payment data
                $paymentData = [];

                if (array_key_exists('payments', $requestedData) &&
                    is_array($requestedData['payments']) && count($requestedData['payments']) > 0) {
                    foreach ($requestedData['payments'] as $payment) {
                        if (is_array($payment)) {
                            $item = [
                                'payable_type' => Relation::getMorphAlias(Worksite::class),
                                'payable_id' => $workSite->uuid,
                                'amount' => $payment['payment_amount'],
                                'payment_date' => Carbon::parse($payment['payment_date']),
                                'payment_type' => PaymentTypesEnum::CASH->value,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            $paymentData[] = $item;
                        }
                    }
                }
                // Perform bulk insert
                if (!empty($paymentData)) {
                    $payments = $workSite->payments()->createMany($paymentData);
                    PaymentCreatedEvent::dispatch($payments, $workSite->customer?->uuid);
                }

                $files = $request->file('images');
                if (isset($files) && is_array($files)) {
                    foreach ($files as $file) {
                        $fileNameParts = explode('.', $file->getClientOriginalName());
                        $fileName = $fileNameParts[0];
                        $path = lcfirst('Worksite');
                        $name = $fileName . '_' . now()->format('YmdH');
                        $relativeName = $path . '/' . $name . '.webp';

                        if (!File::exists(public_path('storage/' . $path))) {
                            File::makeDirectory(public_path('storage/' . $path));
                        }

                        $fullPath = public_path('storage/' . $path) . '/' . $name . '.webp';

                        // create new manager instance with desired driver
                        $manager = new ImageManager(new Driver);

                        // read image from filesystem
                        $manager->read($file)->save($fullPath);
                        $workSite->media()->create([
                            'name' => $name,
                            'file_name' => $relativeName,
                        ]);
                    }
                }
                //        $this->fileManager->upload($files);
            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse(
            new Result);
    }

    /**
     * Show the specified resource.
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     * /
     */
    public function show(int $id): JsonResponse
    {

        $worksite = Worksite::query()->with(['customer', 'items.warehouse', 'media', 'address', 'pendingOrders'])
            ->findOrFail($id);

        $payments = $this->paymentSyncService->getPaymentsForWorksite($worksite);

        $worksite->customerPayments = $payments;

        $worksite->totalPaymentsAmount = number_format((float)$payments->sum('amount'), 2);


        $worksite->items->map(function (Item $item) use ($payments) {

            $item->quantityInWarehouse = $item->warehouse->quantity;
            $item->inStock = $item->warehouse->quantity > WarehouseItemThresholdsEnum::LOW->value ?
                'In-Stock' :
                ($item->warehouse->quantity > 0 ? 'Low-Stock' : 'Off-Stock');
        });

        return ApiResponseHelper::sendSuccessResponse(new Result(WorksiteDetailsResource::make($worksite)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws Throwable
     */
    public function update(WorksiteUpdateRequest $request, int $id): JsonResponse
    {

        DB::transaction(
            callback: function () use ($request, $id) {
                /**
                 * @var array{
                 *  title: string |null,
                 *  description: string|null,
                 *  manager_id?: int|null,
                 *  customer_id?: int|null,
                 *  category_id?: int|null,
                 *  parent_worksite_id?: int|null,
                 *  contractor_id?: float|null,
                 *  starting_budget?: float|null,
                 *  cost?: float|null,
                 *  address?: string|null,
                 *  city_id?: int|null,
                 *  workers_count?: int|null,
                 *  receipt_date?: string|null,
                 *  starting_date?: string|null,
                 *  deliver_date?: string|null,
                 *  reception_status?: int|null,
                 *  completion_status?: int|null,
                 *  items?: array{
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
                $workSite = Worksite::query()->findOrFail($id);

                // update address
                $newAddress = null;
                $address = Address::query()
                    ->when(isset($requestedData['address']), function (Builder $query) use ($requestedData) {
                        $query->where(column: 'title', operator: '=', value: $requestedData['address']);
                    })
                    ->when(isset($requestedData['city_id']), function (Builder $query) use ($requestedData) {
                        $query->where(column: 'city_id', operator: '=', value: $requestedData['city_id']);
                    })
                    ->first();

                if ($address) {
                    $addressDataToUpdate = array_filter([
                        'city_id' => $requestedData['city_id'] ?? null,
                        'title' => $requestedData['address'] ?? null,
                    ], fn($value) => $value != null);
                    $address->update($addressDataToUpdate);
                } else {
                    $newAddress = Address::query()->create([
                        'city_id' => $requestedData['city_id'] ?? null,
                        'title' => $requestedData['address'] ?? null,
                    ]);
                }

                $dataToSave = array_filter([
                    'title' => $requestedData['title'] ?? null,
                    'description' => $requestedData['description'] ?? null,
                    'manager_id' => $requestedData['manager_id'] ?? null,
                    'customer_id' => $requestedData['customer_id'] ?? null,
                    'category_id' => $requestedData['category_id'] ?? null,
                    'contractor_id' => $requestedData['contractor_id'] ?? null,
                    'parent_worksite_id' => $requestedData['parent_worksite_id'] ?? null,
                    'starting_budget' => $requestedData['starting_budget'] ?? null,
                    'cost' => $requestedData['cost'] ?? null,
                    'address_id' => $newAddress != null ? $newAddress->id : $address->id,
                    'workers_count' => $requestedData['workers_count'] ?? null,
                    'receipt_date' => $requestedData['receipt_date'] ?? null,
                    'starting_date' => $requestedData['starting_date'] ?? null,
                    'deliver_date' => $requestedData['deliver_date'] ?? null,
                    'reception_status' => $requestedData['reception_status'] ?? null,
                    'completion_status' => $requestedData['completion_status'] ?? null,
                ], fn($value) => $value != null);

                $workSite->update($dataToSave);

            },
            attempts: 3);

        return ApiResponseHelper::sendSuccessResponse(
            new Result);
    }

    /**
     * @throws UnAbleToCloseWorkSiteException
     */
    public function close(int $id): JsonResponse
    {
        $workSite = Worksite::query()->with(['subWorksites'])->findOrFail($id);
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

        return ApiResponseHelper::sendSuccessResponse();
    }

    /**
     * @throws Throwable
     */
    public function assignContractor(int $workSiteId, int $contractorId): JsonResponse
    {
        $workSite = Worksite::query()->findOrFail($workSiteId);
        Contractor::query()->findOrFail($contractorId);
        $workSite->update([
            'contractor_id' => $contractorId,
        ]);

        return ApiResponseHelper::sendSuccessResponse();
    }

    public function unAssignContractor(int $workSiteId, int $contractorId): JsonResponse
    {
        $workSite = Worksite::query()->findOrFail($workSiteId);
        Contractor::query()->findOrFail($contractorId);

        $workSite->update([
            'contractor_id' => null,
        ]);

        return ApiResponseHelper::sendSuccessResponse();
    }
}
