<?php

namespace fadllabanie\laravel_unittest_generator;

use fadllabanie\laravel_unittest_generator\Console\Commands\UnitGenerator;
use Illuminate\Support\ServiceProvider;

class LaravelUnittestGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Loading routes if your package has routes
       // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Loading views if your package has views
      // $this->loadViewsFrom(__DIR__.'/resources/views', 'laravelUnittestGenerator');

        // Publishing configuration files if your package has config
        // $this->publishes([
        //     __DIR__.'/path/to/config/laravelUnittestGenerator.php' => config_path('laravelUnittestGenerator.php'),
        // ], 'config');

        // ... other bootstrapping logic
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Binding things into the service container
        // $this->app->bind('example', function () {
        //     return new Example();
        // });

        // Registering package commands, if you have any
     
        $this->commands([
            UnitGenerator::class,
        ]);

        // ... other registration logic
    }
}
