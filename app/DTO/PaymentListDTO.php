<?php

namespace App\DTO;

use Spatie\LaravelData\Data;


class PaymentListDTO extends Data
{

    /**
     * @param string|null $dateFrom
     * @param string|null $dateTo
     */
    public function __construct(
        public ?string $dateFrom,
        public ?string $dateTo,
    )
    {
    }

    /**
     * @param array{
     *     date_from:string|null,
     *     date_to:string|null
     * } $request
     * @return self
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            dateFrom: $request['date_from'],
            dateTo: $request['date_to']
        );
    }
}
