<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\NexmoMessage;
use Makeable\DatabaseNotifications\Events\NexmoNotificationSent;

class NexmoChannel extends Channel
{
    use ProxiesNotifications;

    /**
     * @param $data
     * @return mixed
     */
    public function deserialize($data)
    {
        return $this->buildObject(new NexmoMessage, $data);
    }

    /**
     * @return string
     */
    public function notificationSentEvent()
    {
        return NexmoNotificationSent::class;
    }
}
