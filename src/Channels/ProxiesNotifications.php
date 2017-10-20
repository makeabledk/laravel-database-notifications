<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Traits\Macroable;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

trait ProxiesNotifications
{
    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        app(Dispatcher::class)->sendNow(
            $notification->notifiable,
            $this->buildDispatchableNotification($notification),
            $notification->type
        );
    }

    /**
     * Build a dummy notification class that mocks the original 'to' method
     *
     * @param DatabaseNotification $notification
     * @return mixed
     */
    protected function buildDispatchableNotification(DatabaseNotification $notification)
    {
        return tap(new class { use Macroable; },
            function ($dummy) use ($notification) {
                $dummy::macro($this->toMethod(), function () use ($notification) {
                    return $this->deserialize($notification->data);
                });
            }
        );
    }
}