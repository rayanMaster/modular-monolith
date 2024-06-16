<?php

namespace App\DTO;

use App\Enums\PaymentTypesEnum;
use Spatie\LaravelData\Data;

class PaymentCreateDTO extends Data
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
     *  payable_id:int | null,
     *  payable_type:string | null,
     *  payment_type:int | null,
     *  payment_amount:float,
     *  payment_date:string
     * } $request
     */
    public static function fromRequest(array $request): PaymentCreateDTO
    {
        return new self(
            payable_id: $request['payable_id'] ?? null,
            payable_type: $request['payable_type'] ?? null,
            payment_type: $request['payment_type'] ?? PaymentTypesEnum::CASH->value,
            amount: $request['payment_amount'],
            payment_date: $request['payment_date'],
        );
    }
}
