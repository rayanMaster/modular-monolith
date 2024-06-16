<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ResourceCreateDTO extends Data
{
    public function __construct(
        public float $quantity,
        public float $price
    ) {
    }

    /**
     * @param array{
     *     quantity: float,
     *     price: float,
     * } $request
     */
    public static function fromRequest(array $request): ResourceCreateDTO
    {

        return new self(
            quantity: $request['quantity'],
            price: $request['price'],
        );
    }
}
