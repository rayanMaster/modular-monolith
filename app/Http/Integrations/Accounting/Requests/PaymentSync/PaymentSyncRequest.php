<?php

namespace App\Http\Integrations\Accounting\Requests\PaymentSync;

use App\Models\Customer;
use App\Models\Worksite;
use Illuminate\Database\Eloquent\Relations\Relation;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class PaymentSyncRequest extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(private readonly PaymentSyncDTO $DTO)
    {
    }

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/worksite/payment/make_payment';
    }

    protected function defaultBody(): array
    {
        return $this->prepareData();
    }

    public function prepareData(): array
    {
        //'pay_from_type' => 'required|string|in:client,contractor,employee,supplier', // Only allow specific types if applicable
        //'pay_from_uuid' => 'required|uuid', // Assuming it should be a valid UUID
        //'cash_account_id' => 'nullable|integer|exists:cash_accounts,id', // Optional field, must reference a valid cash account ID
        //'pay_to_uuid' => 'required|uuid',
        //'pay_to_type' => 'required|string',
        //'payment_date' => 'required|date', // Must be a valid date
        //'payment_amount' => 'required|numeric', // Positive payment amount
        return [
            'pay_from_type' => Relation::getMorphAlias(Customer::class),
            'pay_from_uuid' => $this->DTO->customer_uuid,
            'pay_to_uuid' => $this->DTO->worksite_uuid,
            'pay_to_type' => Relation::getMorphAlias(Worksite::class),
            'payment_date' => $this->DTO->payment_date,
            'payment_amount' => $this->DTO->payment_amount
        ];
    }
}
