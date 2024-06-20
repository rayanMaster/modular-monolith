<?php

namespace App\DTO;

use App\Enums\WorkSiteCompletionStatusEnum;
use App\Enums\WorkSiteReceptionStatusEnum;
use Spatie\LaravelData\Data;

class WorkSiteCreateDTO extends Data
{
    /**
     * @param array<int,array{
     *     id:int,
     *     quantity:int,
     *     price:float
     * }>|null $workSiteItems
     * @param array<int,array{
     *     payment_amount:float,
     *     payment_date: string
     * }>|null $payments
     */
    public function __construct(
        public string  $title,
        public string  $description,
        public ?int    $customerId,
        public ?int    $categoryId,
        public ?int    $contractorId,
        public ?int    $parentWorksiteId,
        public ?float  $startingBudget,
        public ?float  $cost,
        public ?int    $addressId,
        public ?int    $workersCount,
        public ?string $receiptDate,
        public ?string $startingDate,
        public ?string $deliverDate,
        public ?int    $receptionStatus,
        public ?int    $completionStatus,
        public ?array  $workSiteItems,
        public ?array  $payments,
    )
    {
    }

    /**
     * @param array{
     *  title: string,
     *  description: string,
     *  customer_id?: int|null,
     *  category_id?: int|null,
     *  contractor_id?: int|null,
     *  parent_work_site_id?: int|null,
     *  starting_budget?: float|null,
     *  cost?: float|null,
     *  address_id?: int|null,
     *  workers_count?: int|null,
     *  receipt_date?: string|null,
     *  starting_date?: string|null,
     *  deliver_date?: string|null,
     *  reception_status?: int|null,
     *  completion_status?: int|null,
     *  items?: array<int,array{
     *      id:int,
     *      quantity:int,
     *      price:float
     *  }>|null,
     *  payments?: array<int,array{
     *      payment_amount:float,
     *      payment_date: string
     *  }>|null
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteCreateDTO
    {
        return new self(
            title: $request['title'],
            description: $request['description'],
            customerId: $request['customer_id'] ?? null,
            categoryId: $request['category_id'] ?? null,
            contractorId: $request['contractor_id'] ?? null,
            parentWorksiteId: $request['parent_work_site_id'] ?? null,
            startingBudget: $request['starting_budget'] ?? 0,
            cost: $request['cost'] ?? 0,
            addressId: $request['address_id'] ?? null,
            workersCount: $request['workers_count'] ?? 0,
            receiptDate: $request['receipt_date'] ?? null,
            startingDate: $request['starting_date'] ?? null,
            deliverDate: $request['deliver_date'] ?? null,
            receptionStatus: $request['reception_status'] ?? WorkSiteReceptionStatusEnum::SCRATCH->value,
            completionStatus: $request['completion_status'] ?? WorkSiteCompletionStatusEnum::PENDING->value,
            workSiteItems: $request['items'] ?? null,
            payments: $request['payments'] ?? null,
        );
    }
}
