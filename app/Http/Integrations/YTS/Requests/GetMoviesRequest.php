<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS\Requests;

use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\MovieMetaData;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetMoviesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '';
    }

    /**
     * @return Collection<string, Collection<int, MovieData>|Collection<int, MovieMetaData>>>
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        /** @var array{
         *     movie_count: int,
         *     limit: int,
         *     page_number: int,
         *     movies: array<int, array{
         *                               id: int,
         *                           title_english: string,
         *                           year: int,
         *                           rating: float,
         *                           language: string,
         *                           genres: array<int, string>,
         *                           medium_cover_image: string
         *                          }>
         *     }|null $data
         */
        $data = $response->json()['data'];

        $movies = $data['movies'] ?? [];

        /** @var Collection<int, MovieData> $moviesDTO* */
        $moviesDTO = collect();
        /** @var Collection<int, MovieMetaData> $metaDTO* */
        $metaDTO = collect();

        foreach ($movies as $movie) {
            $moviesDTO->add(
                new MovieData(
                    id: $movie['id'],
                    name: $movie['title_english'],
                    year: $movie['year'],
                    rating: $movie['rating'],
                    language: $movie['language'],
                    genres: $movie['genres'],
                    medium_cover_image: $movie['medium_cover_image'],
                )
            );
        }

        if ($data) {
            $metaData = new MovieMetaData(
                movie_count: $data['movie_count'],
                limit: $data['limit'],
                page: $data['page_number']
            );

            $metaDTO->add($metaData);
        }

        return collect(['movies' => $moviesDTO, 'meta' => $metaDTO]);
    }
}
