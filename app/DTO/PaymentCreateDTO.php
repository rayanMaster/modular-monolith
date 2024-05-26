<?php

namespace App\DTO;

class PaymentCreateDTO extends \Spatie\LaravelData\Data
{
    public function __construct(
        public ?int $work_site_id,
        public ?int $payment_type,
        public float $amount,
        public string $payment_date,
    ) {
    }

    /**
     * @param array{
     *  payment_type:int,
     *  payment_amount:float,
     *  payment_date:string
     * } $request
     */
    public static function fromRequest(array $request, ?int $workSiteId = null): PaymentCreateDTO
    {
        return new self(
            work_site_id: $workSiteId ?? null,
            payment_type: $request['payment_type'] ?? 1,
            amount: $request['payment_amount'],
            payment_date: $request['payment_date'],
        );
    }
}
