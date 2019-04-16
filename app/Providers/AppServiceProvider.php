<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       $this->app->bind('AppService', AppService::class);
	   view()->composer('*', function ($view) {
		$current_route_name = \Route::currentRouteName();
		$view->with('current_route_name', $current_route_name);
	   });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
	
	/**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['AppService'];
    }
}
