<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkSiteResourceAddDTO extends Data
{
    public function __construct(
        public int $quantity,
        public int $price,
    ) {
    }

    /**
     * @param array{
     *    price : int,
     *    quantity : int
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
