<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ML\PythonMLService;

class MLServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PythonMLService::class, function ($app) {
            return new PythonMLService(config('ml'));
        });
    }

    public function boot()
    {
        //
    }
}
