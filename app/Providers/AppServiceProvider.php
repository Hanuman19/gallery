<?php

namespace App\Providers;

use App\Repository\Gallery\GalleryRepository;
use App\Services\Gallery\GalleryService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        App::bind(GalleryService::class, function() {
            return new GalleryService(
                new GalleryRepository()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
