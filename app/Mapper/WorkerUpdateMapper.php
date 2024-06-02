<?php

namespace App\Mapper;

use App\DTO\WorkerCreateDTO;
use App\DTO\WorkerUpdateDTO;

class WorkerUpdateMapper
{
    public static function fromEloquent(WorkerUpdateDTO $workerUpdateDTO): array
    {

        return [
            'first_name' => $workerUpdateDTO->firstName,
        ];
    }
}
