<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class WorkerUpdateDTO extends Data
{
    public function __construct(
        public ?string $firstName = null,
    ) {
    }

    public static function fromRequest(array $request): self
    {
        return new self(
            firstName: $request['first_name'] ?? null,
        );

    }
}
