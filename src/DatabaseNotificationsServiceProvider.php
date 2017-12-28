<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Support\ServiceProvider;
use Makeable\DatabaseNotifications\Channels\Broadcast;
use Makeable\DatabaseNotifications\Channels\Database;
use Makeable\DatabaseNotifications\Channels\Mail;
use Makeable\DatabaseNotifications\Channels\Nexmo;
use Makeable\DatabaseNotifications\Channels\Slack;
use Makeable\DatabaseNotifications\Console\SendPendingNotifications;
use Makeable\DatabaseNotifications\Jobs\SendNotification;

class DatabaseNotificationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Notification::created(function ($notification) {
            if (config('database-notifications.send-immediately') === true) {
                SendNotification::dispatch($notification);
            }
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/database-notifications.php', 'database-notifications');
        $this->loadMigrationsFrom(__DIR__.'/../database');

        $this->app->singleton(DatabaseChannelManager::class, function () {
            return tap(new DatabaseChannelManager, function ($manager) {
                $manager->extend('broadcast', Broadcast::class);
                $manager->extend('database', Database::class);
                $manager->extend('mail', Mail::class);
                $manager->extend('nexmo', Nexmo::class);
                $manager->extend('slack', Slack::class);
            });
        });

        $this->commands([
            SendPendingNotifications::class
        ]);
    }
}