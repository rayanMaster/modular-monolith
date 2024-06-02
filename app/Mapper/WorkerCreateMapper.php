<?php

namespace App\Mapper;

use App\DTO\WorkerCreateDTO;

class WorkerCreateMapper
{
    public static function fromEloquent(WorkerCreateDTO $workerCreateDTO): array
    {

        return [
            'first_name' => $workerCreateDTO->firstName,
        ];
    }
}
