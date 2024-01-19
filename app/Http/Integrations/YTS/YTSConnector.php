<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class YTSConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://yts.mx/api/v2/list_movies.json';
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
