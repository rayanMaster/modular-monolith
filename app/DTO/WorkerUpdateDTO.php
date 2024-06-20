<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkerUpdateDTO extends Data
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $phone,
    ) {
    }

    /**
     * @param array{
     *     first_name:string|null,
     *     last_name:string|null,
     *     phone:string|null
     * } $request
     */
    public static function fromRequest(array $request): WorkerUpdateDTO
    {
        return new self(
            firstName: $request['first_name'] ?? null,
            lastName: $request['last_name'] ?? null,
            phone: $request['phone'] ?? null,
        );

    }
}
