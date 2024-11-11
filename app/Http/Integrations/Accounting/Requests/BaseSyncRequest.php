<?php

namespace App\Http\Integrations\Accounting\Requests;

use App\Http\Integrations\Accounting\BaseDTO\BaseSyncDTO;
use App\Http\Integrations\Accounting\Requests\WorksiteSync\WorksiteSyncDTO;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

abstract class BaseSyncRequest extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(private readonly BaseSyncDTO $DTO) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    abstract public function resolveEndpoint(): string;

    protected function defaultBody(): array
    {
        return $this->prepareData();
    }

    public function prepareData(): array
    {
        return [
            'name' => $this->DTO->name,
            'uuid' => $this->DTO->uuid,
        ];
    }
}
