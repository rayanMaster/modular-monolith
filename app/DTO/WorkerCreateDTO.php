<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkerCreateDTO extends Data
{
    public function __construct(
        public string $firstName,
    ) {
    }

    /**
     * @param array{
     *     first_name:string
     * }$request
     */
    public static function fromRequest(array $request): WorkerCreateDTO
    {
        return new self(
            firstName: $request['first_name'],
        );

    }
}
