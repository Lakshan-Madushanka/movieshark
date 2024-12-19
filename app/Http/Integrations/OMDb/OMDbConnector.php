<?php

declare(strict_types=1);

namespace App\Http\Integrations\OMDb;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class OMDbConnector extends Connector
{
    use AcceptsJson;

    public ?int $tries = 3;

    public ?bool $throwOnMaxTries = false;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.omdbapi.com';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }


    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
