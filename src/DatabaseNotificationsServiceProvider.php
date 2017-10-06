<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Support\ServiceProvider;

class DatabaseNotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database');
    }
}