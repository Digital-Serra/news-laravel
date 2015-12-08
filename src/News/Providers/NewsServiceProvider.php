<?php
/**
 * @author    Mauri de Souza Nunes <mauri870@gmail.com>
 * @copyright Copyright (c) 2015, Mauri de Souza Nunes <github.com/mauri870>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace DigitalSerra\NewsLaravel;


use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //Load routes
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/Http/routes.php';
        }

        //Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'test');

        //Publish Migrations
        $this->publishes([
            __DIR__.'/resources/migrations/' => database_path('/migrations')
        ], 'migrations');
    }

    public function register()
    {

    }
}