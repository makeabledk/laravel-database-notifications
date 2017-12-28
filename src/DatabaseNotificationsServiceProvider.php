<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Support\ServiceProvider;
use Makeable\DatabaseNotifications\Channels\BroadcastChannel;
use Makeable\DatabaseNotifications\Channels\DatabaseChannel;
use Makeable\DatabaseNotifications\Channels\MailChannel;
use Makeable\DatabaseNotifications\Channels\NexmoChannel;
use Makeable\DatabaseNotifications\Channels\SlackChannel;
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
                $manager->extend('broadcast', BroadcastChannel::class);
                $manager->extend('database', DatabaseChannel::class);
                $manager->extend('mail', MailChannel::class);
                $manager->extend('nexmo', NexmoChannel::class);
                $manager->extend('slack', SlackChannel::class);
            });
        });

        $this->commands([
            SendPendingNotifications::class
        ]);
    }
}