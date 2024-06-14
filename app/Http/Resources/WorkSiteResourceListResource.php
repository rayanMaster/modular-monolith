<?php

namespace App\Http\Resources;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property Resource $resource
 * @property mixed $pivot
 */
class WorkSiteResourceListResource extends JsonResource
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
            'quantity' => $this->pivot->quantity,
            'price' => $this->pivot->price,
            'resource' => $this->resource?->name
        ];
    }
}
