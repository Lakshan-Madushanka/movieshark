<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS\Requests;

use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\MovieResponse;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetMovieSuggestionsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly int $id) {}

    public function resolveEndpoint(): string
    {
        return '/movie_suggestions.json';
    }

    protected function defaultQuery(): array
    {
        return [
            'movie_id' => $this->id,
        ];
    }

    /**
     * @param  MovieResponse  $response
     * @return Collection<int, MovieData>
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->getMovieListData();
        $movies = $data['movies'] ?? [];

        $movies = collect($movies)->filter(fn($movie) => $movie['id'] !== 0)->all();

        $suggestions = collect();

        foreach ($movies as $movie) {
            $suggestions->add(
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

        return $suggestions;
    }
}
