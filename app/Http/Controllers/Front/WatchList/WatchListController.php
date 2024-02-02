<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front\WatchList;

use App\Http\Controllers\Controller;
use App\Http\Requests\WatchList\WatchListStoreRequest;
use App\Models\WatchList;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class WatchListController extends Controller
{
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
