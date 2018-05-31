<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Support\ServiceProvider;
use Makeable\DatabaseNotifications\Channels\BroadcastChannel;
use Makeable\DatabaseNotifications\Channels\DatabaseChannel;
use Makeable\DatabaseNotifications\Channels\MailChannel;
use Makeable\DatabaseNotifications\Channels\NexmoChannel;
use Makeable\DatabaseNotifications\Channels\SlackChannel;
use Makeable\DatabaseNotifications\Channels\SparkChannel;
use Makeable\DatabaseNotifications\Console\SendPendingNotifications;
use Makeable\DatabaseNotifications\Jobs\SendNotification;

class DatabaseNotificationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateMakeableDatabaseNotificationsTable')) {
            $this->publishes([
                __DIR__.'/../database/create_makeable_database_notifications_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_makeable_database_notifications_table.php'),
            ], 'migrations');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/database-notifications.php', 'database-notifications');
        $this->publishes([__DIR__.'/../config/database-notifications.php' => config_path('database-notifications.php')], 'config');

        $this->commands(SendPendingNotifications::class);

        Notification::created(function ($notification) {
            if (config('database-notifications.send-immediately') === true && ! $notification->sent_at) {
                SendNotification::dispatch($notification);
            }
        });
    }

    public function register()
    {
        $this->app->singleton(DatabaseChannelManager::class, function () {
            return tap(new DatabaseChannelManager, function ($manager) {
                $manager->extend('broadcast', BroadcastChannel::class);
                $manager->extend('database', DatabaseChannel::class);
                $manager->extend('mail', MailChannel::class);
                $manager->extend('nexmo', NexmoChannel::class);
                $manager->extend('slack', SlackChannel::class);
                $manager->extend('spark', SparkChannel::class);
            });
        });
    }
}
