<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

/**
 *
 */
class WorkSiteResourceAddDTO extends Data
{
    /**
     * @param int $quantity
     * @param int $price
     */
    public function __construct(
        public int $quantity,
        public int $price,
    )
    {}

    /**
     * @param array{
     *    price : int,
     *    quantity : int
     * } $request
     * @return WorkSiteResourceAddDTO
     */
    public static function fromRequest(array $request): WorkSiteResourceAddDTO
    {
        return new self(
            quantity: $request['quantity'],
            price: $request['price']
        );

    }

}
