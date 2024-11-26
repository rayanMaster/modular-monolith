<?php

namespace App\Http\Resources;

use App\Enums\WorksiteCompletionStatusEnum;
use App\Enums\WorksiteReceptionStatusEnum;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Models\WorksiteCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $title
 * @property mixed $description
 * @property Customer|null $customer
 * @property User $manager
 * @property WorksiteCategory|null $category
 * @property mixed $subWorksites
 * @property mixed $starting_budget
 * @property mixed $cost
 * @property mixed $address
 * @property mixed $workers_count
 * @property Collection<Order>|null $pendingOrders
 * @property mixed $receipt_date
 * @property mixed $starting_date
 * @property mixed $deliver_date
 * @property mixed $reception_status
 * @property mixed $completion_status
 * @property string $created_at
 * @property string $updated_at
 * @property mixed $resources
 * @property mixed $payments
 * @property mixed $customerPayments
 */
class WorksiteListResource extends JsonResource
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
            'manager' => $this->manager->fullName,
            'customer' => $this->customer?->fullName,
            'category' => $this->category?->name,
            'sub_worksites' => $this->subWorksites,
            'starting_budget' => $this->starting_budget,
            'cost' => $this->cost,
            'address' => $this->address?->rawAddress,
            'pending_orders_count' => $this->pendingOrders?->count(),
            'workers_count' => $this->workers_count,
            'receipt_date' => $this->receipt_date,
            'starting_date' => $this->starting_date,
            'deliver_date' => $this->deliver_date,
            'reception_status' => WorksiteReceptionStatusEnum::from($this->reception_status)->name,
            'completion_status' => WorksiteCompletionStatusEnum::from($this->completion_status)->name,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDateTimeString(),
            'payments' => WorksitePaymentListResource::collection($this->customerPayments ?? collect()),
        ];
    }
}
