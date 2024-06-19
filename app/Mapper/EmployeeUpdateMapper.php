<?php

namespace App\Mapper;

use App\DTO\WorkerUpdateDTO;

class EmployeeUpdateMapper
{
    /**
     * @param WorkerUpdateDTO $workerUpdateDTO
     * @return array{
     *     first_name:string|null
     * }
     */
    public static function fromEloquent(WorkerUpdateDTO $workerUpdateDTO): array
    {

        return [
            'first_name' => $workerUpdateDTO->firstName,
        ];
    }
}
