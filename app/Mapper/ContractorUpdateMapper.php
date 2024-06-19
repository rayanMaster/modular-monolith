<?php

namespace App\Mapper;

use App\DTO\ContractorUpdateDTO;

class ContractorUpdateMapper
{
    /**
     * @param ContractorUpdateDTO $createDTO
     * @return array{
     *     first_name:string|null,
     *     last_name:string|null,
     *     phone:string|null,
     *     address_id:int|null,
     * }
     */
    public static function toEloquent(ContractorUpdateDTO $createDTO): array
    {
        return [
            'first_name' => $createDTO->firstName ?? null,
            'last_name' => $createDTO->lastName ?? null,
            'phone' => $createDTO->phone ?? null,
            'address_id' => $createDTO->addressId ?? null,
        ];
    }
}
