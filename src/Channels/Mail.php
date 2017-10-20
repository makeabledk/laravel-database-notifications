<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\MailMessage;

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
}