<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front\WatchList;

use App\Http\Controllers\Controller;
use App\Http\Requests\WatchList\WatchListStoreRequest;
use App\Models\WatchList;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class WatchListController extends Controller
{
    public function index(Request $request): Response
    {
        $watchList = WatchList::applyFilters($request)
            ->paginate(10);

        return Inertia::render(
            component: 'Dashboard',
            props: ['watchList' => $watchList]
        );
    }

    public function store(WatchListStoreRequest $request): Redirector|RedirectResponse|Application
    {
        $payload = $request->payload();

        if ($record = WatchList::query()->where('yts_id', $payload->yts_id)->first()) {
            $record->delete();
        } else {
            WatchList::query()->create($payload->toArray());
        }

        return redirect(route('home.index'));
    }
}
