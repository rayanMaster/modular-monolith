<?php

namespace App\DTO;

use App\Enums\WorkSiteCompletionStatusEnum;
use App\Enums\WorkSiteReceptionStatusEnum;

class WorkSiteCreateDTO extends \Spatie\LaravelData\Data
{
    /**
     * @param string $title
     * @param string $description
     * @param int|null $customerId
     * @param int|null $categoryId
     * @param int|null $parentWorksiteId
     * @param int|null $startingBudget
     * @param int|null $cost
     * @param int|null $addressId
     * @param int|null $workersCount
     * @param string|null $receiptDate
     * @param string|null $startingDate
     * @param string|null $deliverDate
     * @param int|null $receptionStatus
     * @param int|null $completionStatus
     * @param array|null $workSiteResources
     * @param array|null $payments
     * @param FileDTO|null $image
     */
    public function __construct(
        public string $title,
        public string $description,
        public ?int $customerId,
        public ?int $categoryId,
        public ?int $parentWorksiteId,
        public ?int $startingBudget,
        public ?int $cost,
        public ?int $addressId,
        public ?int $workersCount,
        public ?string $receiptDate,
        public ?string $startingDate,
        public ?string $deliverDate,
        public ?int $receptionStatus,
        public ?int $completionStatus,
        public ?array $workSiteResources,
        public ?array $payments,
        public ?FileDTO $image // Adjust namespace according to your application
    ) {
    }

    /**
     * @param array{
     *  title: string,
     *  description: string,
     *  customer_id?: int|null,
     *  category_id?: int|null,
     *  parent_worksite_id?: int|null,
     *  starting_budget?: float|null,
     *  cost?: float|null,
     *  address_id?: int|null,
     *  workers_count?: int|null,
     *  receipt_date?: string|null,
     *  starting_date?: string|null,
     *  deliver_date?: string|null,
     *  reception_status?: int|null,
     *  completion_status?: int|null,
     *  resources?: array|null,
     *  payments: array|null,
     *  image?: array|null
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteCreateDTO
    {
        return new self(
            title: $request['title'],
            description: $request['description'],
            customerId: $request['customer_id'] ?? null,
            categoryId: $request['category_id'] ?? null,
            parentWorksiteId: $request['parent_worksite_id'] ?? null,
            startingBudget: $request['starting_budget'] ?? 0,
            cost: $request['cost'] ?? 0,
            addressId: $request['address_id'] ?? null,
            workersCount: $request['workers_count'] ?? 0,
            receiptDate: $request['receipt_date'] ?? null,
            startingDate: $request['starting_date'] ?? null,
            deliverDate: $request['deliver_date'] ?? null,
            receptionStatus: $request['reception_status'] ?? WorkSiteReceptionStatusEnum::SCRATCH->value,
            completionStatus: $request['completion_status'] ?? WorkSiteCompletionStatusEnum::PENDING->value,
            workSiteResources: $request['resources'] ?? null,
            payments: $request['payments'] ?? null,
            image: FileDTO::fromRequest($request)
        );
    }
}
