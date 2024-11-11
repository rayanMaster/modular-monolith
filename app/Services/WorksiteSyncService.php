<?php

namespace App\Services;

use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\WorksiteSync\WorksiteSyncDTO;
use App\Http\Integrations\Accounting\Requests\WorksiteSync\WorksiteSyncRequest;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

readonly class WorksiteSyncService
{
    public function __construct(
        private AccountingConnector $accountingConnector
    ) {}

    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function syncWorksiteToAccounting(WorksiteSyncDTO $DTO): void
    {
        $request = new WorksiteSyncRequest($DTO);

        $this->accountingConnector->send($request);
    }

}
