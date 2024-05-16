<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front\WatchList;

use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\MovieListStoreRequest;
use App\Models\Movie;
use App\Models\WatchList;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WatchListController extends Controller
{
    public function create(): Response
    {
        return Inertia::render(component: 'Dashboard');
    }

    public function index(Request $request): Response
    {
        $watchList = Auth::user()
            ->movies()
            ->filter()
            ->Sort()
            ->paginate(10);

        return Inertia::render(
            component: 'Dashboard',
            props: ['watchList' => $watchList]
        );
    }

    public function toggle(Request $request): RedirectResponse
    {
        $ytsId = $request->input('yts_id');

        /** @var Movie $record */
        if ($record = Auth::user()->movies()->where('yts_id', $ytsId)->first()) {
            $record->delete();
            return redirect()->back();
        }

        return $this->store(app(MovieListStoreRequest::class));
    }

    public function store(MovieListStoreRequest $request): Redirector|RedirectResponse|Application
    {
        $payload = $request->payload();

        Auth::user()->watchList->movies()->create($payload->toArray());

        return back();
    }

    public function edit(Movie $movie): Response
    {
        return Inertia::render(
            component: 'Dashboard',
            props: ['movie' => $movie]
        );
    }

    public function update(string $movieId, MovieListStoreRequest $request): RedirectResponse
    {
        $movie = Auth::user()->movies()->where('movies.id', $movieId)->firstOrFail();

        $movie->update($request->payload()->toArray());

        return back();
    }

    public function destroy(string $movieId): RedirectResponse
    {
        Auth::user()->movies()->where('movies.id', $movieId)->delete();

        return back();
    }
}
