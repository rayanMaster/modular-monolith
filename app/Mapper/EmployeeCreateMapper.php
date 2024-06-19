<?php

namespace App\Mapper;

use App\DTO\WorkerCreateDTO;

class EmployeeCreateMapper
{
    /**
     * @param WorkerCreateDTO $workerCreateDTO
     * @return array{
     *     first_name:string|null
     * }
     */
    public static function fromEloquent(WorkerCreateDTO $workerCreateDTO): array
    {

        return [
            'first_name' => $workerCreateDTO->firstName,
        ];
    }
}
