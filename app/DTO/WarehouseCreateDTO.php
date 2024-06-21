<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WarehouseCreateDTO extends Data
{

    /**
     * @param string $name
     * @param string|null $addressId
     */
    public function __construct(
        public string $name,
        public ?string $addressId,
    )
    {
    }

    /**
     * @param array{
     *     name:string,
     *     address_id:int|null
     * } $request
     * @return self
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            name: $request['name'],
            addressId: $request['address_id'],
        );
    }
}
