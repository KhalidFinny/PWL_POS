<?php

namespace App\Providers;
use App\Models\BarangModel;
use App\Observers\BarangObserver;

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
    public function boot()
    {
        BarangModel::observe(BarangObserver::class);
    }
}
