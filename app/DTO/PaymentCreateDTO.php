<?php

namespace App\DTO;

class PaymentCreateDTO extends \Spatie\LaravelData\Data
{
    public function __construct(
        public ?int $payable_id,
        public ?string $payable_type,
        public ?int $payment_type,
        public float $amount,
        public string $payment_date,
    ) {
    }

    /**
     * @param array{
     *  payable_id:int,
     *  payable_type:string,
     *  payment_type:int,
     *  payment_amount:float,
     *  payment_date:string
     * } $request
     */
    public static function fromRequest(array $request, ?int $workSiteId = null): PaymentCreateDTO
    {
        return new self(
            payable_id: $request['payable_id'] ?? null,
            payable_type: $request['payable_type'] ?? null,
            payment_type: $request['payment_type'] ?? 1,
            amount: $request['payment_amount'],
            payment_date: $request['payment_date'],
        );
    }
}
