<?php

namespace App\Services;

use App\Enums\PaymentTypesEnum;
use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\GetWorkSitePayment\GetWorksitePaymentsDTO;
use App\Http\Integrations\Accounting\Requests\GetWorkSitePayment\GetWorksitePaymentsRequest;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncDTO;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncRequest;
use App\Http\Integrations\Accounting\Requests\WorksiteSync\WorksiteSyncDTO;
use App\Http\Integrations\Accounting\Requests\WorksiteSync\WorksiteSyncRequest;
use App\Models\Worksite;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use function Pest\Laravel\json;

readonly class PaymentSyncService
{
    public function __construct(
        private AccountingConnector $accountingConnector
    )
    {
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function syncPaymentsToAccounting(PaymentSyncDTO $DTO): void
    {
        $request = new PaymentSyncRequest($DTO);

        $this->accountingConnector->send($request);
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function getPaymentsForWorksite(Worksite $worksite): Collection
    {
        $customerUUIDs = [];
        if ($worksite->customer?->uuid != null) $customerUUIDs = [$worksite->customer?->uuid];
        $requestDTO = new GetWorksitePaymentsDTO(worksiteUUID: $worksite->uuid, customerUUIDs: $customerUUIDs);
        $request = new GetWorksitePaymentsRequest($requestDTO);
        $jsonResponse = array();
        if (!empty($customerUUIDs))
            $jsonResponse = $this->accountingConnector->send($request)?->json();

        return $this->normalizePaymentsData($jsonResponse);
    }

    private function normalizePaymentsData(array $response): Collection
    {

        $payments = collect();

        $mainData = !empty($response) ? $response['data'] : [];
        if (is_array($mainData) && sizeof($mainData) > 0) {
            $data = $mainData[0]['data'];
            $payments = collect($data)->map(function ($item) {
                return (object)[
                    'amount' => $item['amount'],
                    'payment_date' => $item['created_at'],
                    'payment_type' => PaymentTypesEnum::CASH->value
                ];
            });
        }
        return $payments;
    }

}
