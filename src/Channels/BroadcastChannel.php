<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Makeable\DatabaseNotifications\Events\BroadcastNotificationSent;

class BroadcastChannel extends Channel
{
    use ProxiesNotifications;

    /**
     * @param $data
     * @return mixed
     */
    public function deserialize($data)
    {
        return new BroadcastMessage($data);
    }

    /**
     * @return string
     */
    public function notificationSentEvent()
    {
        return BroadcastNotificationSent::class;
    }
}
