<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Traits\Macroable;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

trait ProxiesNotifications
{
    /**
     * @param $data
     * @return mixed
     */
    abstract public function deserialize($data);

    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        app(Dispatcher::class)->sendNow(
            $notification->notifiable,
            $this->buildDispatchableNotification($notification),
            [method_exists($this, 'originalDriver') ? $this->originalDriver() : $notification->channel]
        );

        if (method_exists($this, 'notificationSentEvent')) {
            call_user_func_array(
                [$this->notificationSentEvent(), 'dispatch'],
                [$notification->notifiable, $notification]
            );
        }
    }

    /**
     * Build a dummy notification class that mocks the original 'to' method.
     *
     * @param DatabaseNotification $notification
     * @return mixed
     */
    protected function buildDispatchableNotification(DatabaseNotification $notification)
    {
        $payload = $this->deserialize($notification->data);

        $class = new class extends Notification {
            use Macroable;
        };
        $class::macro($this->toMethod(), function () use ($payload) {
            return $payload;
        });

        return tap(new $class, function ($recipe) use ($notification) {
            $recipe->id = $notification->id;
        });
    }
}
