<?php

namespace App\Mapper;

use App\DTO\WorkSiteCreateDTO;
use App\Enums\PaymentTypesEnum;
use Carbon\Carbon;

class WorkSiteCreateMapper extends \Spatie\LaravelData\Data
{
    public static function toWorkSiteEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {
        return [
            'title' => $workSiteDTO->title,
            'description' => $workSiteDTO->description,
            'customer_id' => $workSiteDTO->customerId,
            'category_id' => $workSiteDTO->categoryId,
            'parent_worksite_id' => $workSiteDTO->parentWorksiteId,
            'starting_budget' => $workSiteDTO->startingBudget,
            'cost' => $workSiteDTO->cost,
            'address_id' => $workSiteDTO->addressId,
            'workers_count' => $workSiteDTO->workersCount,
            'receipt_date' => $workSiteDTO->receiptDate,
            'starting_date' => $workSiteDTO->startingDate,
            'deliver_date' => $workSiteDTO->deliverDate,
            'status_on_receive' => $workSiteDTO->statusOnReceive,
        ];
    }

    public static function toWorkSiteResourcesEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {

        $result = [];
        if ($workSiteDTO->workSiteResources && count($workSiteDTO->workSiteResources) > 0) {
            foreach ($workSiteDTO->workSiteResources as $resource) {
                $item = [
                    'quantity' => $resource['quantity'],
                    'price' => $resource['price'],
                ];
                $result[$resource['id']] = $item;
            }
        }

        return $result;

    }

    public static function toPaymentEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {
        $result = [];
        if ($workSiteDTO->payments && count($workSiteDTO->payments) > 0) {
            foreach ($workSiteDTO->payments as $payment) {
                $item = [
                    'amount' => $payment['payment_amount'],
                    'payment_date' => Carbon::parse($payment['payment_date']),
                    'payment_type' => PaymentTypesEnum::CASH->value,
                ];
                $result[] = $item;
            }
        }

        return $result;
    }
}
