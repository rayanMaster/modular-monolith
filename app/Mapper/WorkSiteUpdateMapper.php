<?php

namespace App\Mapper;

use App\DTO\WorkSiteUpdateDTO;
use Spatie\LaravelData\Data;

class WorkSiteUpdateMapper extends Data
{
    /**
     * @return array{
     * title: string|null,
     * description: string|null,
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
