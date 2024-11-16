<?php

namespace App\Http\Integrations\Accounting\Requests\WorksiteSync;

use App\Http\Integrations\Accounting\Requests\BaseSyncRequest;

class WorksiteSyncRequest extends BaseSyncRequest
{
    public function __construct(public readonly WorksiteSyncDTO $worksiteSyncDTO)
    {
        parent::__construct($worksiteSyncDTO);
    }

    public function resolveEndpoint(): string
    {
        return '/worksite';
    }
}
