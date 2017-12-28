<?php

namespace Makeable\DatabaseNotifications\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Makeable\DatabaseNotifications\Notification;
use Illuminate\Queue\SerializesModels;

class DatabaseNotificationSent
{
    use Dispatchable, SerializesModels;

    /**
     * @var mixed
     */
    public $notifiable;

    /**
     * @var Notification
     */
    public $notification;

    /**
     * @param $notifiable
     * @param Notification $notification
     */
    public function __construct($notifiable, Notification $notification)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
    }
}