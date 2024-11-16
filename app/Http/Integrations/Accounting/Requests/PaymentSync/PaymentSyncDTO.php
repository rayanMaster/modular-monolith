<?php

namespace App\Http\Integrations\Accounting\Requests\PaymentSync;

use Spatie\LaravelData\Data;

class PaymentSyncDTO extends Data
{
    public function __construct(
        public string $customer_uuid,
        public string $worksite_uuid,
        public string $payment_date,
        public float $payment_amount,
        public ?int $cash_account_id = null
    ) {}
}
