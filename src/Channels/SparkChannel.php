<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\DatabaseNotification;
use Laravel\Spark\Notifications\SparkChannel as BaseSparkChannel;
use Makeable\DatabaseNotifications\Events\SparkNotificationSent;

class SparkChannel extends BaseSparkChannel
{
    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        SparkNotificationSent::dispatch($notification->notifiable, $notification);
    }
}
