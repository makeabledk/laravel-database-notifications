<?php

namespace Makeable\DatabaseNotifications\Channels;

use Makeable\DatabaseNotifications\Events\DatabaseNotificationSent;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

class Database extends Channel
{
    /**
     * @param $data
     * @return mixed
     */
    public function deserialize($data)
    {
        return $data;
    }

    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        // No action required
    }

    /**
     * @return string
     */
    public function notificationSentEvent()
    {
        return DatabaseNotificationSent::class;
    }
}