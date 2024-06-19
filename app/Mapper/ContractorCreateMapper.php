<?php

namespace App\Mapper;

use App\DTO\ContractorCreateDTO;
use App\DTO\ContractorUpdateDTO;

class ContractorCreateMapper
{
    /**
     * @param ContractorCreateDTO $createDTO
     * @return array{
     *     first_name:string,
     *     last_name:string|null,
     *     phone:string|null,
     *     address_id:int|null,
     * }
     */
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
