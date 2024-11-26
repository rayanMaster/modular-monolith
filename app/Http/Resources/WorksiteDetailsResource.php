<?php

namespace App\Http\Resources;

use App\Enums\WorkSiteCompletionStatusEnum;
use App\Enums\WorkSiteReceptionStatusEnum;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Models\WorksiteCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $title
 * @property mixed $description
 * @property Customer $customer
 * @property User $manager
 * @property WorksiteCategory $category
 * @property mixed $subWorksites
 * @property mixed $starting_budget
 * @property mixed $cost
 * @property Address $address
 * @property Order $pendingOrders
 * @property string $totalPaymentsAmount
 * @property mixed $workers_count
 * @property mixed $receipt_date
 * @property mixed $starting_date
 * @property mixed $deliver_date
 * @property mixed $reception_status
 * @property mixed $completion_status
 * @property string $created_at
 * @property string $updated_at
 * @property mixed $items
 * @property mixed $payments
 * @property mixed $customerPayments
 * @property mixed $media
 */
class WorksiteDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'customer' => $this->customer?->fullName,
            'manager' => $this->manager?->fullName,
            'category' => $this->category?->name,
            'sub_worksites' => WorksiteDetailsResource::collection($this->subWorksites),
            'starting_budget' => $this->starting_budget,
            'cost' => $this->cost,
            'address' => $this->address?->rawAddress,
            'pending_orders_count' => $this->pendingOrders?->count(),
            'total_payments_amount' => $this->totalPaymentsAmount,
            'workers_count' => $this->workers_count,
            'receipt_date' => $this->receipt_date,
            'starting_date' => $this->starting_date,
            'deliver_date' => $this->deliver_date,
            'reception_status' => WorkSiteReceptionStatusEnum::from($this->reception_status)->name,
            'completion_status' => WorkSiteCompletionStatusEnum::from($this->completion_status)->name,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDateTimeString(),
            'payments' => WorksitePaymentListResource::collection($this->customerPayments ?? collect()),
            'items' => WorksiteItemListResource::collection($this->items),
            'media' => MediaListResource::collection($this->media),
        ];
    }
}
