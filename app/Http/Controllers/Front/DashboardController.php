<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class DashboardController extends Controller
{
    public function index(Request $httpRequest): Application|RedirectResponse|Redirector
    {
        return redirect(route('movies-watch-list.index'));
    }
}
