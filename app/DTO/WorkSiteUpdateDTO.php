<?php

namespace App\DTO;

use App\Enums\WorkSiteReceptionStatusEnum;
use Spatie\LaravelData\Data;

class WorkSiteUpdateDTO extends Data
{
    /**
     * @param array{
     *   id:int,
     *   quantity:int,
     *   price:float
     * }|null $workSiteResources
     * @param array{
     *   payment_amount:float,
     *   payment_date: string
     * }|null $payments
     */
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?int $customerId,
        public ?int $categoryId,
        public ?int $parentWorkSiteId,
        public ?float $startingBudget,
        public ?float $cost,
        public ?int $addressId,
        public ?int $workersCount,
        public ?string $receiptDate,
        public ?string $startingDate,
        public ?string $deliverDate,
        public ?int $receptionStatus,
        public ?array $workSiteResources,
        public ?array $payments,
    ) {
    }

    /**
     * @param array{
     *  title: string|null,
     *  description: string|null,
     *  customer_id?: int|null,
     *  category_id?: int|null,
     *  parent_work_site_id?: int|null,
     *  starting_budget?: float|null,
     *  cost?: float|null,
     *  address_id?: int|null,
     *  workers_count?: int|null,
     *  receipt_date?: string|null,
     *  starting_date?: string|null,
     *  deliver_date?: string|null,
     *  reception_status?: int|null,
     * resources?: array{
     *    id:int,
     *    quantity:int,
     *    price:float
     * }|null,
     * payments?: array{
     *    payment_amount:float,
     *    payment_date: string
     * }|null
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteUpdateDTO
    {
        return new self(
            title: $request['title'] ?? null,
            description: $request['description'] ?? null,
            customerId: $request['customer_id'] ?? null,
            categoryId: $request['category_id'] ?? null,
            parentWorkSiteId: $request['parent_work_site_id'] ?? null,
            startingBudget: $request['starting_budget'] ?? 0,
            cost: $request['cost'] ?? 0,
            addressId: $request['address_id'] ?? null,
            workersCount: $request['workers_count'] ?? null,
            receiptDate: $request['receipt_date'] ?? null,
            startingDate: $request['starting_date'] ?? null,
            deliverDate: $request['deliver_date'] ?? null,
            receptionStatus: $request['reception_status'] ?? WorkSiteReceptionStatusEnum::SCRATCH->value,
            workSiteResources: $request['resources'] ?? null,
            payments: $request['payments'] ?? null,
        );
    }
}
