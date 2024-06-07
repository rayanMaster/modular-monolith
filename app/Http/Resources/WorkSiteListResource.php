<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkSiteListResource extends JsonResource
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
            'customer' => $this->customer->fullName,
            'category' => $this->category->name,
            'main_worksite' => $this->main_worksite?->title,
            'starting_budget' => $this->starting_budget,
            'cost' => $this->cost,
            'address' => $this->address,
            'workers_count' => $this->workers_count,
            'receipt_date' => $this->receipt_date,
            'starting_date' => $this->starting_date,
            'deliver_date' => $this->deliver_date,
            'status_on_receive' => $this->status_on_receive,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDateTimeString(),
            'payments' => $this->payments,
        ];
    }
}
