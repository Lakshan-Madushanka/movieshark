<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front\Movies;

use App\Http\Controllers\Controller;
use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\Requests\GetMovieDetailsRequest;
use App\Http\Integrations\YTS\Requests\GetMovieSuggestionsRequest;
use App\Http\Integrations\YTS\YTSConnector;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class MoviesController extends Controller
{
    public function show(int $id): Response
    {
        $ytsConnector = new YTSConnector();
        $ytsMovieDetailsRequest = new GetMovieDetailsRequest($id);
        $ytsMovieSuggestionsRequest = new GetMovieSuggestionsRequest($id);

        /** @var array{}|MovieData $ytsMovieDetailsResponseData */
        $ytsMovieDetailsResponseData = $ytsConnector->send($ytsMovieDetailsRequest)->dtoOrFail();
        /** @var Collection<int, MovieData> $ytsMovieSuggestionsResponseData */
        $ytsMovieSuggestionsResponseData = $ytsConnector->send($ytsMovieSuggestionsRequest)->dtoOrFail();

        //        dd($ytsMovieDetailsResponseData);

        return Inertia::render(
            component: 'Movies/Show',
            props: [
                'movie' => $ytsMovieDetailsResponseData,
                'suggestions' => $ytsMovieSuggestionsResponseData,
            ]
        );
    }
}
