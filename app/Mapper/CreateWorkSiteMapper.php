<?php

namespace App\Mapper;

use App\DTO\WorkSiteCreateDTO;

class CreateWorkSiteMapper extends \Spatie\LaravelData\Data
{
    public static function toWorkSiteEloquent(WorkSiteCreateDTO $workSiteDTO): array
    {
        return [
            'title' => $workSiteDTO->title,
            'description' => $workSiteDTO->description,
            'customer_id' => $workSiteDTO->customerId,
            'category_id' => $workSiteDTO->categoryId,
            'main_worksite' => $workSiteDTO->mainWorksite,
            'starting_budget' => $workSiteDTO->startingBudget,
            'cost' => $workSiteDTO->cost,
            'address' => $workSiteDTO->address,
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

    public static function toPaymentEloquent(WorkSiteCreateDTO $workSiteDTO, $id): array
    {
        $result = [];
        if ($workSiteDTO->payments && count($workSiteDTO->payments) > 0) {
            foreach ($workSiteDTO->payments as $payment) {
                $item = [
                    'work_site_id' => $id,
                    'amount' => $payment['payment_amount'],
                    'payment_date' => $payment['payment_date'],
                    'payment_type' => 1,
                ];
                $result[] = $item;
            }
        }

        return $result;
    }
}
