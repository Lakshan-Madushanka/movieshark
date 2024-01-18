<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Integrations\YTS\Requests\GetMoviesRequest;
use App\Http\Integrations\YTS\YTSConnector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(): \Inertia\Response
    {
        $yts = new YTSConnector;
        $request = new GetMoviesRequest;

        $response = $yts->send($request);

        //dd($response->json()['data']['movies']);

        return Inertia::render(
            component: 'Home',
            props: [
                'movies' => $response->dto(),
            ]
        );
    }
}
