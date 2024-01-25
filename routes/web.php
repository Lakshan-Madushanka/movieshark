<?php

declare(strict_types=1);

use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\Movies\MoviesController;
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
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
});

Route::name('home.')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('index');
});

Route::get('/movies/{id}', [MoviesController::class, 'show'])->name('movies.show');
