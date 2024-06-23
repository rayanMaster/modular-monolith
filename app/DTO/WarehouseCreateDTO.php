<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WarehouseCreateDTO extends Data
{
    public function __construct(
        public string $name,
        public ?int $addressId,
    ) {
    }

    /**
     * @param array{
     *     name:string,
     *     address_id:int|null
     * } $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            name: $request['name'],
            addressId: $request['address_id'],
        );
    }
}
