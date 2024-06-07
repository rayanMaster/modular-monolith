<?php

namespace App\DTO;

use App\Enums\WorkSiteStatusesEnum;

class WorkSiteUpdateDTO extends \Spatie\LaravelData\Data
{
    /**
     * @param ResourceCreateDTO|null $workSiteResourceDTO
     * @param PaymentCreateDTO|null $paymentDTO
     */
    public function __construct(
        public ?string   $title,
        public ?string   $description,
        public ?int     $customerId,
        public ?int     $categoryId,
        public ?bool    $mainWorksite,
        public ?int     $startingBudget,
        public ?int     $cost,
        public ?int     $address,
        public ?int     $workersCount,
        public ?string  $receiptDate,
        public ?string  $startingDate,
        public ?string  $deliverDate,
        public ?int     $statusOnReceive,
        public ?array   $workSiteResources,
        public ?array   $payments,
        public ?FileDTO $image // Adjust namespace according to your application
    )
    {
    }

    /**
     * @param array{
     *  title: string,
     *  description: string,
     *  customer_id?: int|null,
     *  category_id?: int|null,
     *  main_worksite?: bool|null,
     *  starting_budget?: float|null,
     *  cost?: float|null,
     *  address?: string|null,
     *  workers_count?: int|null,
     *  receipt_date?: string|null,
     *  starting_date?: string|null,
     *  deliver_date?: string|null,
     *  status_on_receive?: int|null,
     *  resources?: array|null,
     *  payments: array|null,
     *  image?: array|null
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteUpdateDTO
    {
        return new self(
            title: $request['title'] ?? null,
            description: $request['description'] ?? null,
            customerId: $request['customer_id'] ?? null,
            categoryId: $request['category_id'] ?? null,
            mainWorksite: $request['main_worksite'] ?? null,
            startingBudget: $request['starting_budget'] ?? 0,
            cost: $request['cost'] ?? 0,
            address: $request['address'] ?? 0,
            workersCount: $request['workers_count'] ?? null,
            receiptDate: $request['receipt_date'] ?? null,
            startingDate: $request['starting_date'] ?? null,
            deliverDate: $request['deliver_date'] ?? null,
            statusOnReceive: $request['status_on_receive'] ?? WorkSiteStatusesEnum::SCRATCH->value,
            workSiteResources: $request['resources'] ?? null,
            payments: $request['payments'] ?? null,
            image: FileDTO::fromRequest($request) ?? null
        );
    }
}
