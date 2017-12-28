<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\NexmoMessage;

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
}
