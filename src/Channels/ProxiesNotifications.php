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
            method_exists($this, 'originalDriver') ? $this->originalDriver() : $notification->type
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
        $class = new class { use Macroable; };
        $class::macro($this->toMethod(), function () use ($notification) {
            return $this->deserialize($notification->data);
        });

        return tap(new $class, function ($recipe) use ($notification) {
            $recipe->id = $notification->id;
        });
    }
}