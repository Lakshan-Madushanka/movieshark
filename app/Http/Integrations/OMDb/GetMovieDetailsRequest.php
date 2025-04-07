<?php

declare(strict_types=1);

namespace App\Http\Integrations\OMDb;

use Illuminate\Support\Arr;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetMovieDetailsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly string $imdbId) {}

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultQuery(): array
    {
        return [
            'i' => $this->imdbId,
            'plot' => 'full',
            'apikey' => config('services.omdb.token'),
        ];
    }

    /**
     * @param  Response  $response
     * @return MovieData
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): MovieData
    {
        $data = $response->json();

        return new MovieData(
            plot: Arr::get($data, 'Plot', 'N/A'),
        );
    }
}
