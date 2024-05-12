<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Integrations\YTS\MovieData;
use App\Http\Integrations\YTS\MovieMetaData;
use App\Http\Integrations\YTS\Requests\GetMoviesRequest;
use App\Http\Integrations\YTS\YTSConnector;
use App\Models\WatchList;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $httpRequest): Response
    {
        /** @var array<string, mixed> $queryString */
        $queryString = $httpRequest->query();

        $yts = new YTSConnector();

        $ytsRequest = new GetMoviesRequest();
        $ytsRequest->query()->merge($queryString);

        /** @var Collection<string, Collection<int, MovieData>|Collection<int, MovieMetaData>> $ytsResponseData */
        $ytsResponseData = $yts->send($ytsRequest)->dtoOrFail();

        $watchListIds = WatchList::query()->pluck('yts_id');

        return Inertia::render(
            component: 'Home',
            props: [
                'movies' => $ytsResponseData->get('movies'),
                'meta' => $ytsResponseData->get('meta'),
                'watchListIds' => $watchListIds,
            ],
        );
    }

    public function about(): Response
    {
        return Inertia::render(
            component: 'About',
        );
    }

    public function terms(): Response
    {
        return Inertia::render(
            component: 'TermsOfService',
        );
    }
}
