<?php

namespace App\Services;

use App\Enums\GeneralSettingNumericEnum;
use App\Enums\PaymentTypesEnum;
use App\Helpers\CacheHelper;
use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\GetWorkSitePayment\GetWorksitePaymentsDTO;
use App\Http\Integrations\Accounting\Requests\GetWorkSitePayment\GetWorksitePaymentsRequest;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncDTO;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncRequest;
use App\Models\Worksite;
use Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class PaymentSyncService
{
    public function __construct(
        private readonly AccountingConnector $accountingConnector,
        private readonly CacheHelper $cacheHelper
    ) {}

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
        $tag = 'worksite_payments';
        return Cache::remember(
            key: $this->cacheHelper->generateCacheKey(tag: $tag, key: $worksite->uuid),
            ttl: GeneralSettingNumericEnum::TTL->value,
            callback: function () use ($worksite) {
                \Log::info("herrrr");
                return $this->fetchPayments($worksite);
            });

    }

    public function fetchPayments(Worksite $worksite): Collection
    {
        $customerUUIDs = [];
        if ($worksite->customer?->uuid != null) {
            $customerUUIDs = [$worksite->customer?->uuid];
        }
        $requestDTO = new GetWorksitePaymentsDTO(worksiteUUID: $worksite->uuid, customerUUIDs: $customerUUIDs);
        $request = new GetWorksitePaymentsRequest($requestDTO);

        $jsonResponse = [];
        if (! empty($customerUUIDs)) {
            $jsonResponse = $this->accountingConnector->send($request)?->json();
        }

        return $this->normalizePaymentsData($jsonResponse);
    }

    private function normalizePaymentsData(array $response): Collection
    {

        $payments = collect();

        $mainData = ! empty($response) && array_key_exists('data',$response) ? $response['data'] : [];
        if (is_array($mainData) && count($mainData) > 0) {
            $data = $mainData[0]['data'];
            $payments = collect($data)->map(function ($item) {
                return (object) [
                    'amount' => $item['amount'],
                    'payment_date' => $item['created_at'],
                    'payment_type' => PaymentTypesEnum::CASH->value,
                ];
            });
        }

        return $payments;
    }
}
