<?php

namespace Caffeinated\Repository;

use Caffeinated\Repository\Listeners\RepositoryEventListener;
use Illuminate\Support\ServiceProvider;

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
