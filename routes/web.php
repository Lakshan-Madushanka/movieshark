<?php

declare(strict_types=1);

use App\Http\Controllers\Front\DashboardController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\Movies\MoviesController;
use App\Http\Controllers\Front\WatchList\WatchListController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::name('home.')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('index');
});

Route::name('movies.')->prefix('/movies')->group(function (): void {
    Route::get('/{id}', [MoviesController::class, 'show'])->name('show');
});
Route::get('movies-browse', [MoviesController::class, 'browse'])->name('movies.browse');

Route::middleware(['auth:sanctum'])
    ->name('movies-watch-list.')
    ->prefix('/movies-watch-list')
    ->group(function (): void {
        Route::get('/', [WatchListController::class, 'index'])->name('index');
        Route::get('/create', [WatchListController::class, 'create'])->name('create');
        Route::post('/store', [WatchListController::class, 'store'])->name('store');
        Route::delete('{watchList}/destroy', [WatchListController::class, 'store'])->name('destroy');
        Route::post('/toggle', [WatchListController::class, 'toggle'])->name('toggle');
    });
