<?php

namespace App\DTO;

class ResourceCreateDTO extends \Spatie\LaravelData\Data
{
    public function __construct(
        public int $id,
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
