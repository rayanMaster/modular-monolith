<?php

namespace App\Mapper;

use App\DTO\WorkSiteUpdateDTO;

class WorkSiteUpdateMapper extends \Spatie\LaravelData\Data
{
    public static function toWorkSiteEloquent(WorkSiteUpdateDTO $workSiteDTO): array
    {
        return [
            'title' => $workSiteDTO->title,
            'description' => $workSiteDTO->description,
            'customer_id' => $workSiteDTO->customerId,
            'category_id' => $workSiteDTO->categoryId,
            'parent_work_site_id' => $workSiteDTO->parentWorkSiteId,
            'starting_budget' => $workSiteDTO->startingBudget,
            'cost' => $workSiteDTO->cost,
            'address_id' => $workSiteDTO->addressId,
            'workers_count' => $workSiteDTO->workersCount,
            'receipt_date' => $workSiteDTO->receiptDate,
            'starting_date' => $workSiteDTO->startingDate,
            'deliver_date' => $workSiteDTO->deliverDate,
            'reception_status' => $workSiteDTO->receptionStatus,
        ];
    }
}
