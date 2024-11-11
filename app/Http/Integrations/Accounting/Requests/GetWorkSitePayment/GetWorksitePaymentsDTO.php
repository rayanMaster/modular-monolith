<?php

namespace App\Http\Integrations\Accounting\Requests\GetWorkSitePayment;

use App\Http\Integrations\Accounting\BaseDTO\BaseSyncDTO;
use Date;

class GetWorksitePaymentsDTO extends Date
{
    public function __construct(
        public string $worksiteUUID,
        public array  $customerUUIDs,
    )
    {
    }
}
