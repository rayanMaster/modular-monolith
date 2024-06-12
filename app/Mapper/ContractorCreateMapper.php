<?php

namespace App\Mapper;

use App\DTO\ContractorCreateDTO;

class ContractorCreateMapper
{
    public static function toEloquent(ContractorCreateDTO $createDTO): array
    {
        return [
            'first_name' => $createDTO->firstName,
            'last_name' => $createDTO->lastName ?? null,
            'phone' => $createDTO->phone ?? null,
            'address_id' => $createDTO->addressId ?? null,
        ];
    }
}
