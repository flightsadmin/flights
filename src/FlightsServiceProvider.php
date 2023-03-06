<?php

namespace Flightsadmin\Flights;;

use Flightsadmin\Flights\Commands\LivewireInstall;
use Illuminate\Support\ServiceProvider;

class FlightsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Registering package commands.
            $this->commands([
				LivewireInstall::class,
			]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->singleton('flights', function () {
            return new Flights;
        });
    }
}
