<?php

namespace App\Http\Integrations\Accounting\BaseDTO;

use Spatie\LaravelData\Data;

class BaseSyncDTO extends Data
{
    public function __construct(
        public string $uuid,
        public string $name
    ) {}
}
