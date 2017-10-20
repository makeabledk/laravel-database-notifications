<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Support\ServiceProvider;
use Makeable\DatabaseNotifications\Channels\Mail;
use Makeable\DatabaseNotifications\Channels\Nexmo;
use Makeable\DatabaseNotifications\Channels\Slack;

class DatabaseNotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database');

        $this->app->singleton(DatabaseChannelManager::class, function () {
            return tap(new DatabaseChannelManager, function ($manager) {
                $manager->extend('mail', Mail::class);
                $manager->extend('nexmo', Nexmo::class);
                $manager->extend('slack', Slack::class);
            });
        });
    }
}