<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

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
        // Always fetch from DB on every view load (no cache)
        View::composer(['frontend.layout', 'layouts.admin'], function ($view) {
            $view->with('settings', Setting::first());
        });
    }
}
