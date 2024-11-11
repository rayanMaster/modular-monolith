<?php

namespace App\Http\Integrations\Accounting\Requests\CustomerSync;

use App\Http\Integrations\Accounting\Requests\BaseSyncRequest;

class CustomerSyncRequest extends BaseSyncRequest
{

    public function __construct(CustomerSyncDTO $customerSyncDTO)
    {
        parent::__construct($customerSyncDTO);
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
       return '/customer';
    }
}
