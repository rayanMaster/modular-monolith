<?php

namespace App\Http\Integrations\Accounting\Requests\GetWorksitePayment;

use Date;

class GetWorksitePaymentsDTO extends Date
{
    public function __construct(
        public string $worksiteUUID,
        public array $customerUUIDs,
    ) {}
}
