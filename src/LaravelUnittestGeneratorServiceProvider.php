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
          // Check if the application is running in the console
          if ($this->app->runningInConsole()) {
            // Publish migrations
            $this->publishes([
                __DIR__ . '/database/migrations' => database_path('migrations'),
            ], 'laravel-unittest-generator-migrations');

            // Publish models
            $this->publishes([
                __DIR__ . '/Models' => app_path('Models'),
            ], 'laravel-unittest-generator-models');

            // Publish traits
            $this->publishes([
                __DIR__ . '/Traits' => app_path('Traits'),
            ], 'laravel-unittest-generator-traits');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            UnitGenerator::class,
        ]);
    }
}
