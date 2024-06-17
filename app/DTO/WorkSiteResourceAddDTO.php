<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkSiteResourceAddDTO extends Data
{
    public function __construct(
        public float $quantity,
        public float $price,
    ) {
    }

    /**
     * @param array{
     *    price : float,
     *    quantity : float
     * } $request
     */
    public static function fromRequest(array $request): WorkSiteResourceAddDTO
    {
        return new self(
            quantity: $request['quantity'],
            price: $request['price']
        );

    }
}
