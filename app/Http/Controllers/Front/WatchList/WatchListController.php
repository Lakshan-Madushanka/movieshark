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
    public function create(): Response
    {
        return Inertia::render(component: 'Dashboard');
    }

    public function index(Request $request): Response
    {
        $watchList = WatchList::Filter()
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

        /** @var WatchList $record */
        if ($record = WatchList::query()->where('yts_id', $ytsId)->first()) {
            return $this->destroy($record);
        }

        return $this->store(app(WatchListStoreRequest::class));
    }

    public function store(WatchListStoreRequest $request): Redirector|RedirectResponse|Application
    {
        $payload = $request->payload();

        WatchList::query()->create($payload->toArray());

        return back();
    }

    public function edit(WatchList $watchList): Response
    {

        return Inertia::render(
            component: 'Dashboard',
            props: ['watchList' => $watchList]
        );
    }

    public function update(WatchList $watchList, WatchListStoreRequest $request): RedirectResponse
    {
        $watchList->update($request->payload()->toArray());

        return back();
    }

    public function destroy(WatchList $watchList): RedirectResponse
    {
        $watchList->delete();

        return back();
    }
}
