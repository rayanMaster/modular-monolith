<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkerCreateDTO extends Data
{
    public function __construct(
        public string $firstName,
    ){}

    public static function fromRequest(array $request): self
    {
        return new self(
            firstName: $request['first_name'],
        );

    }

}
