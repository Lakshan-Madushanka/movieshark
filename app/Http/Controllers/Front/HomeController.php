<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Integrations\YTS\Requests\GetMoviesRequest;
use App\Http\Integrations\YTS\YTSConnector;
use Illuminate\Http\Request;
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

        $ytsResponse = $yts->send($ytsRequest);

        return Inertia::render(
            component: 'Home',
            props: [
                'movies' => $ytsResponse->dtoOrFail()->get('movies'),
                'meta' => $ytsResponse->dtoOrFail()->get('meta')->first(),
            ]
        );
    }
}
