<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\MailMessage;
use Makeable\DatabaseNotifications\Events\MailNotificationSent;

class Mail extends Channel
{
    use ProxiesNotifications;

    /**
     * @param $data
     * @return mixed
     */
    public function deserialize($data)
    {
        return $this->buildObject(new MailMessage, $data);
    }

    /**
     * @return string
     */
    public function notificationSentEvent()
    {
        return MailNotificationSent::class;
    }
}