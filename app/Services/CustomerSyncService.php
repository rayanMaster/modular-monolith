<?php

namespace App\Services;

use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\CustomerSync\CustomerSyncDTO;
use App\Http\Integrations\Accounting\Requests\CustomerSync\CustomerSyncRequest;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

readonly class CustomerSyncService
{
    public function __construct(
        private AccountingConnector $accountingConnector
    ) {}

    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function syncCustomerToAccounting(CustomerSyncDTO $DTO): void
    {
        $request = new CustomerSyncRequest($DTO);

        $this->accountingConnector->send($request);
    }
}
