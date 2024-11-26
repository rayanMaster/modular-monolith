<?php

namespace App\Http\Integrations\Accounting\Connector;

use App\Http\Integrations\BaseConnector\BaseConnector;

class AccountingConnector extends BaseConnector
{
    public function __construct() {}

    public function resolveBaseUrl(): string
    {
        return 'http://accounting_api/api/v1';
    }
}
