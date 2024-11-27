<?php

namespace App\Http\Integrations\Accounting\Connector;

use App\Http\Integrations\BaseConnector\BaseConnector;

class AccountingConnector extends BaseConnector
{
    public function __construct()
    {
    }

    public function resolveBaseUrl(): string
    {
        $baseUrl = config('external-service-api.accounting');
        return 'http://' . $baseUrl . '/api/v1';
    }
}
