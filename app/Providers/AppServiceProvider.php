<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        seo()
            ->site('Movieshark - Browse and search movies.')
            ->description('Browse and search movies and may more functionalities.')
            ->withUrl()
            ->locale('en_US')
            ->twitter()
            ->twitterCreator('epmadushanka')
            ->image(asset('images/og-image.png'))
            ->favicon();
    }
}
