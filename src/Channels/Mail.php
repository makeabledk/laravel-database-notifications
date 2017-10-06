<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Makeable\DatabaseNotifications\Message;

class Mail extends Channel
{
    /**
     * @param $data
     * @return mixed
     */
    public function deserialize($data)
    {
        return $this->applyProperties($data, new MailMessage);
    }
}