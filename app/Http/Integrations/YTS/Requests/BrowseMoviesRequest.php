<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS\Requests;

use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\MovieMetaData;
use App\Http\Integrations\YTS\MovieResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class BrowseMoviesRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private HttpRequest $httpRequest) {}

    public function resolveEndpoint(): string
    {
        return '/list_movies.json';
    }

    /**
     * @param  MovieResponse  $response
     * @return Collection<string, Collection<int, MovieMetaData|array{}>>
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->getMovieListData();

        $movies = $data['movies'] ?? [];

        if (count($movies) > 0) {
            $movies = collect($movies)->filter(
                fn($value) => str($value['title_english'])->lower()->contains(strtolower($this->httpRequest->query('query_term'))),
            );
        }

        /** @var Collection<int, MovieData|array{}> $moviesData */
        $moviesData = collect();

        foreach ($movies as $movie) {
            $moviesData->add(
                new MovieData(
                    id: $movie['id'],
                    name: $movie['title_english'],
                    year: $movie['year'],
                    rating: $movie['rating'],
                    language: $movie['language'],
                    genres: $movie['genres'] ?? [],
                    cover_image: $movie['medium_cover_image'],
                ),
            );
        }


        return collect(['movies' => $moviesData]);
    }
}
