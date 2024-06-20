<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkerCreateDTO extends Data
{
    public function __construct(
        public string $firstName,
        public ?string $lastName,
        public string $phone,
        public ?string $password,
    ) {
    }

    /**
     * @param array{
     *     first_name:string,
     *     last_name:string|null,
     *     phone:string,
     *     password:string|null
     * } $request
     */
    public static function fromRequest(array $request): WorkerCreateDTO
    {
        return new self(
            firstName: $request['first_name'],
            lastName: $request['last_name'] ?? null,
            phone: $request['phone'],
            password: $request['password'] ?? '12345678',
        );

    }
}
