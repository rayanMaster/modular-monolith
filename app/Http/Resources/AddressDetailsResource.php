<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'city'=>$this->city?->name,
            'street'=>$this->street,
            'state'=>$this->sstate,
            'zipCode'=>$this->zipcode
        ];
    }
}
