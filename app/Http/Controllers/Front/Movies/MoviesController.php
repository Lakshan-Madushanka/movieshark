<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front\Movies;

use App\Http\Controllers\Controller;
use App\Http\Integrations\OMDb\GetMovieDetailsRequest as GetOMDbMovieDetailsRequest;
use App\Http\Integrations\OMDb\MovieData as OMDbMovieData;
use App\Http\Integrations\OMDb\OMDbConnector;
use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\Requests\BrowseMoviesRequest;
use App\Http\Integrations\YTS\Requests\GetMovieDetailsRequest;
use App\Http\Integrations\YTS\Requests\GetMovieSuggestionsRequest;
use App\Http\Integrations\YTS\YTSConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

use function request;

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

        $watchListHas = Auth::user()?->movies()->where('yts_id', $id)->exists();

        if (empty($ytsMovieDetailsResponseData->description_full)) {
            $plot = $this->loadPlot($ytsMovieDetailsResponseData->imdb_code);
            $ytsMovieDetailsResponseData = $ytsMovieDetailsResponseData->toArray();
            $ytsMovieDetailsResponseData['description_full'] = $plot;
        }

        return Inertia::render(
            component: 'Movies/Show',
            props: [
                'movie' => $ytsMovieDetailsResponseData,
                'suggestions' => $ytsMovieSuggestionsResponseData,
                'watchListHas' => $watchListHas,
                'allowTorrent' => config('app.allow_torrenting'),
                'additionalData' => Inertia::lazy(fn() => $this->loadPlot(request()->query('imdbId'))),
            ],
        );
    }

    public function browse(Request $httpRequest)
    {
        /** @var array<string, mixed> $queryString */
        $queryString = $httpRequest->query();

        $yts = new YTSConnector();

        $ytsRequest = new BrowseMoviesRequest($httpRequest);
        $ytsRequest->query()->merge($queryString);

        $ytsResponseData = $yts->send($ytsRequest)->dtoOrFail();

        return redirect()->back()->with('browsedMovieData', $ytsResponseData->get('movies'));
    }

    public function loadPlot(string $imdbId): array|OMDbMovieData
    {
        $omdbConnector = new OMDbConnector();
        $omdbMovieDetailsRequest = new GetOMDbMovieDetailsRequest($imdbId);

        $omdbMovieDetailsResponse = $omdbConnector->send($omdbMovieDetailsRequest);

        $omdbMovieDetailsResponseData = [];

        if ($omdbMovieDetailsResponse->successful()) {
            $omdbMovieDetailsResponseData = $omdbMovieDetailsResponse->dto();
        }

        return $omdbMovieDetailsResponseData;
    }
}
