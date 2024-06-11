<?php

namespace App\Mapper;

use App\DTO\ContractorCreateDTO;
use App\DTO\ContractorUpdateDTO;

class ContractorUpdateMapper
{
    public static function toEloquent(ContractorUpdateDTO $createDTO): array
    {
        return [
            'first_name' => $createDTO->firstName ?? null,
            'last_name' => $createDTO->lastName ?? null,
            'phone' => $createDTO->phone ?? null,
            'address_id' => $createDTO->addressId ?? null
        ];
    }
}
