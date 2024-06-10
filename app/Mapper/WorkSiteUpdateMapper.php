<?php

namespace App\Mapper;

use App\DTO\WorkSiteCreateDTO;
use App\DTO\WorkSiteUpdateDTO;
use App\Enums\PaymentTypesEnum;
use Carbon\Carbon;

class WorkSiteUpdateMapper extends \Spatie\LaravelData\Data
{
    public static function toWorkSiteEloquent(WorkSiteUpdateDTO $workSiteDTO): array
    {
        return [
            'title' => $workSiteDTO->title,
            'description' => $workSiteDTO->description,
            'customer_id' => $workSiteDTO->customerId,
            'category_id' => $workSiteDTO->categoryId,
            'parent_worksite_id' => $workSiteDTO->parentWorkSiteId,
            'starting_budget' => $workSiteDTO->startingBudget,
            'cost' => $workSiteDTO->cost,
            'address' => $workSiteDTO->address,
            'workers_count' => $workSiteDTO->workersCount,
            'receipt_date' => $workSiteDTO->receiptDate,
            'starting_date' => $workSiteDTO->startingDate,
            'deliver_date' => $workSiteDTO->deliverDate,
            'reception_status' => $workSiteDTO->receptionStatus,
        ];
    }

}
