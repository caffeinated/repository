<?php

namespace Caffeinated\Repository;

use Illuminate\Support\ServiceProvider;
use Caffeinated\Repository\Listeners\RepositoryEventListener;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('caffeinated.repository.listener', RepositoryEventListener::class);
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->subscribe('caffeinated.repository.listener');
    }
}
