<?php

namespace App\Mapper;

use App\DTO\WorkSiteCreateDTO;
use App\Enums\PaymentTypesEnum;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class WorkSiteCreateMapper extends Data
{
    /**
     * @return array{
     * title: string,
     * description: string,
     * customer_id: int|null,
     * category_id: int|null,
     * parent_work_site_id: int|null,
     * starting_budget: float|null,
     * cost: float|null,
     * address_id: int|null,
     * workers_count: int|null,
     * receipt_date: string|null,
     * starting_date: string|null,
     * deliver_date: string|null,
     * reception_status: int|null
     * }
     */
    public static function toWorkSiteEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {
        return [
            'title' => $workSiteDTO->title,
            'description' => $workSiteDTO->description,
            'customer_id' => $workSiteDTO->customerId,
            'category_id' => $workSiteDTO->categoryId,
            'contractor_id' => $workSiteDTO->contractorId,
            'parent_work_site_id' => $workSiteDTO->parentWorksiteId,
            'starting_budget' => $workSiteDTO->startingBudget,
            'cost' => $workSiteDTO->cost,
            'address_id' => $workSiteDTO->addressId,
            'workers_count' => $workSiteDTO->workersCount,
            'receipt_date' => $workSiteDTO->receiptDate,
            'starting_date' => $workSiteDTO->startingDate,
            'deliver_date' => $workSiteDTO->deliverDate,
            'reception_status' => $workSiteDTO->receptionStatus,
            'completion_status' => $workSiteDTO->completionStatus,
        ];
    }

    /**
     * @return array<int,array{
     *     quantity:int,
     *     price:float
     * }>
     */
    public static function toWorkSiteResourcesEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {
        $result = [];
        if (is_array($workSiteDTO->workSiteResources) && count($workSiteDTO->workSiteResources) > 0) {
            foreach ($workSiteDTO->workSiteResources as $resource) {
                if (is_array($resource)) {
                    $item = [
                        'quantity' => $resource['quantity'],
                        'price' => $resource['price'],
                    ];
                    $result[$resource['id']] = $item;
                }
            }
        }
        return $result;

    }

    /**
     * @param WorkSiteCreateDTO $workSiteDTO
     * @return array<int,array{
     *      amount:float,
     *      payment_date:string,
     *      payment_type:int
     *     }>
     */
    public static function toPaymentEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {
        $result = [];
        if (is_array($workSiteDTO->payments) && count($workSiteDTO->payments) > 0) {
            foreach ($workSiteDTO->payments as $payment) {
                if (is_array($payment)) {
                    $item = [
                        'amount' => $payment['payment_amount'],
                        'payment_date' => Carbon::parse($payment['payment_date']),
                        'payment_type' => PaymentTypesEnum::CASH->value,
                    ];
                    $result[] = $item;
                }
            }
        }

        return $result;
    }
}
