<?php

namespace App\Http\Integrations\Accounting\Requests\GetWorksitePayment;

use App\Enums\ChartOfAccountNamesEnum;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetWorksitePaymentsRequest extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(public readonly GetWorksitePaymentsDTO $getWorksitePaymentsDTO) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/worksite/payment/get_payments';
    }

    protected function defaultBody(): array
    {
        return $this->prepareData();
    }

    public function prepareData(): array
    {
        $paymentFromUUIDs = [];
        foreach ($this->getWorksitePaymentsDTO->customerUUIDs as $customerUUID) {
            if (! is_null($customerUUID)) {
                $customers = [
                    'source' => ChartOfAccountNamesEnum::CLIENTS->value,
                    'uuid' => $customerUUID,
                ];
                $paymentFromUUIDs[] = $customers;
            }
        }

        return [
            'uuid' => $this->getWorksitePaymentsDTO->worksiteUUID,
            'payment_from_uuids' => $paymentFromUUIDs,
        ];
    }
}
