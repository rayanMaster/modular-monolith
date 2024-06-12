<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ContractorCreateDTO extends Data
{
    public function __construct(
        public string $firstName,
        public ?string $lastName,
        public ?string $phone,
        public ?int $addressId
    ) {
    }

    /**
     * @param array{
     *     first_name : string,
     *     last_name : string|null,
     *     phone: string | null,
     *     address_id:int|null
     * } $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            firstName: $request['first_name'],
            lastName: $request['last_name'] ?? null,
            phone: $request['phone'] ?? null,
            addressId: $request['address_id'] ?? null
        );

    }
}
