<?php

namespace App\Http\Integrations\YTS\Requests;

use App\Http\Integrations\YTS\MovieData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetMoviesRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $movies = $response->json()['data']['movies'];

        //dd($data['movies']);
        $moviesDto = collect();

        foreach ($movies as $movie) {
            $moviesDto->add(
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

        return $moviesDto;
    }
}
