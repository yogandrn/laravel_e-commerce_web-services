<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\ProductPicture;
use App\Observers\ProductObserver;
use App\Observers\ProductPictureObserver;
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
        // the observer models
        Product::observe(ProductObserver::class);
        ProductPicture::observe(ProductPictureObserver::class);
    }
}
