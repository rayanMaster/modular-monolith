<?php

namespace App\Mapper;

use App\DTO\WorkerCreateDTO;

class EmployeeCreateMapper
{
    /**
     * @param WorkerCreateDTO $workerCreateDTO
     * @return array{
     *     first_name:string,
     *     last_name:string|null,
     *     phone:string,
     *     password:string|null
     * }
     */
    public static function fromEloquent(WorkerCreateDTO $workerCreateDTO): array
    {

        return [
            'first_name' => $workerCreateDTO->firstName,
            'last_name' => $workerCreateDTO->lastName,
            'phone' => $workerCreateDTO->phone,
            'password'=>$workerCreateDTO->password,
        ];
    }
}
