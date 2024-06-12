<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'resource_category' => ResourceCategoryDetailsResource::make($this->category),
            'work_site_id' => $this->pivot?->work_site_id,
            'resource_id' => $this->pivot?->resource_id,
            'price' => $this->pivot?->price,
            'quantity' => $this->pivot?->quantity,
        ];
    }
}
