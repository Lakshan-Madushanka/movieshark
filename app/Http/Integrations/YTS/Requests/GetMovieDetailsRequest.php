<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS\Requests;

use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\MovieResponse;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetMovieDetailsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly int $id) {}

    public function resolveEndpoint(): string
    {
        return '/movie_details.json';
    }

    protected function defaultQuery(): array
    {
        return [
            'movie_id' => $this->id,
            'with_images' => 'true',
        ];
    }

    /**
     * @param  MovieResponse  $response
     * @return array{}|MovieData
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): MovieData|array
    {
        $data = $response->getMovieDetails();

        $movie = $data['movie'] ?? [];

        $torrents = $response->buildTorrentData();

        $movieData = [];

        if ($movie) {
            $movieData = new MovieData(
                id: $movie['id'],
                name: $movie['title_english'],
                year: $movie['year'],
                rating: $movie['rating'],
                language: $movie['language'],
                genres: $movie['genres'] ?? [],
                cover_image: $movie['medium_cover_image'],
                mpa_rating: $movie['mpa_rating'],
                imdb_code: $movie['imdb_code'],
                runtime: $movie['runtime'],
                description_full: $movie['description_full'],
                like_count: $movie['like_count'],
                yt_trailer_code: $movie['yt_trailer_code'],
                image1: $movie['large_screenshot_image1'],
                image2: $movie['large_screenshot_image2'],
                image3: $movie['large_screenshot_image3'],
                torrents: $torrents,
            );
        }

        return $movieData;
    }
}
