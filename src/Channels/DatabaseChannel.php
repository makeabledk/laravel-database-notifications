<?php

namespace Makeable\DatabaseNotifications\Channels;

use Makeable\DatabaseNotifications\Events\DatabaseNotificationSent;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

class DatabaseChannel extends Channel
{
    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        DatabaseNotificationSent::dispatch($notification->notifiable, $notification);
    }
}
