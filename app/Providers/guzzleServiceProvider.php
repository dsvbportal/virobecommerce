<?php

namespace App\Providers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
class guzzleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->bind('guzzle', function()
        {
            return new \App\Helpers\guzzle;
        });		
    }
}
