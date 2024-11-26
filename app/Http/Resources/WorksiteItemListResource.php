<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property Item $item
 * @property string $name
 * @property object{
 *     quantity:int,
 *     price:float
 * } $pivot
 */
class WorksiteItemListResource extends JsonResource
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
            'quantity_in_warehouse' => $this->quantityInWarehouse,
            'quantity_in_worksite' => $this->pivot->quantity,
            'in_stock' => $this->inStock,
            'price' => $this->pivot->price,
            'name' => $this->name,
        ];
    }
}
