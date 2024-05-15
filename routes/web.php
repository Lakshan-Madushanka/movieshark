<?php

declare(strict_types=1);

use App\Http\Controllers\Front\DashboardController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\Movies\MoviesController;
use App\Http\Controllers\Front\WatchList\WatchListController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])
    ->name('dashboard')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/watch-list', [DashboardController::class, 'watchList'])->name('.watchList');
    });

Route::name('home.')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/terms-and-conditions', [HomeController::class, 'terms'])->name('terms');
});

Route::name('movies.')->prefix('/movies')->group(function (): void {
    Route::get('/{id}', [MoviesController::class, 'show'])->name('show');
});
Route::get('movies-browse', [MoviesController::class, 'browse'])->name('movies.browse');

Route::middleware(['auth:sanctum'])
    ->name('watch-list-movies.')
    ->prefix('/watch-list-movies')
    ->group(function (): void {
        Route::get('/', [WatchListController::class, 'index'])->name('index');
        Route::get('/create', [WatchListController::class, 'create'])->name('create');
        Route::post('/store', [WatchListController::class, 'store'])->name('store');
        Route::get('{movie}/edit', [WatchListController::class, 'edit'])->name('edit');
        Route::put('{movieId}/update', [WatchListController::class, 'update'])->name('update');
        Route::delete('{movieId}/destroy', [WatchListController::class, 'destroy'])->name('destroy');
        Route::post('/toggle', [WatchListController::class, 'toggle'])->name('toggle');
    });
