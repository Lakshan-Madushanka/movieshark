<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Traits\Plugins\AcceptsJson;

class YTSConnector extends Connector
{
    use AcceptsJson;

    const BASE_URLS = [
        'https://yts.lt/api/v2',
        'https://yts.mx/api/v2',
        'https://yts.am/api/v2',
    ];

    protected ?string $response = MovieResponse::class;

    public ?int $tries = 3;

    private $attempts = 0;

    public ?bool $throwOnMaxTries = false;

    public function __construct()
    {
        $this->tries = count(self::BASE_URLS);
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return self::BASE_URLS[$this->attempts];
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

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        $this->attempts++;

        return true;
    }
}
