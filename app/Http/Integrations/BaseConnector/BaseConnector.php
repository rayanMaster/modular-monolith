<?php

namespace App\Http\Integrations\BaseConnector;

use Lang;
use Saloon\Exceptions\SaloonException;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

abstract class BaseConnector extends Connector
{
    use AcceptsJson,AlwaysThrowOnErrors;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return '';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Accept-Language' => Lang::getLocale(),
        ];
    }

    /**
     * Default HTTP client options
     */
    public function defaultConfig(): array
    {
        return [
            'timeout' => 3600,
        ];
    }

    /**
     * @throws SaloonException
     */
    public function hasRequestFailed(Response $response): ?bool
    {
        $res = json_decode($response->body());
        if ($response->status() == 200) {
            return false;
        }
        throw new SaloonException($res->message, $response->status());
    }
}
