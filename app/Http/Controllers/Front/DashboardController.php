<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $meta = Auth::user()->movies()->toBase()
            ->selectRaw("count(case when watched_status is not null then 1 end) as watched")
            ->selectRaw("count(case when watched_status is null then 1 end) as unWatched")
            ->selectRaw("count(case when downloaded_status is not null then 1 end) as downloaded")
            ->selectRaw("count(case when downloaded_status is null then 1 end) as notDownloaded")
            ->selectRaw("count(case when my_rating < 34 then 1 end) as lowPreferred")
            ->selectRaw("count(case when my_rating between 34 and 66 then 1 end) as mediumPreferred")
            ->selectRaw("count(case when my_rating > 66 then 1 end) as highPreferred")
            ->selectRaw("count(1) as 'all'")
            ->first();

        $startDate = Carbon::create($request->query('year', now()->year))->startOfYear();
        $endDate = Carbon::create($request->query('year', now()->year))->endOfYear();

        $moviesHistory = Auth::user()->movies()->filter()->toBase()
            ->select(DB::raw("strftime('%m', movies.created_at) as month, COUNT(*) as total"))
            ->whereBetween('movies.created_at', [
                $startDate,
                $endDate,
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $watchList = Auth::user()->movies()->limit(10)->latest()->get();

        return Inertia::render(
            component: 'Dashboard',
            props: compact('meta', 'moviesHistory', 'watchList'),
        );
    }

    public function watchList(Request $httpRequest): Application|RedirectResponse|Redirector
    {
        return redirect(route('watch-list-movies.index'));
    }
}
