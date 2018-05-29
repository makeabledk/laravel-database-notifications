<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Notification;
use Laravel\Spark\Spark;
use Makeable\DatabaseNotifications\Events\SparkNotificationSent;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

class SparkChannel extends Channel
{
    /*
    * @param $notifiable
    * @param Notification $notification
    */
    public function send($notifiable, Notification $template)
    {
        if (method_exists($notifiable, 'routeNotificationForSpark')) {
            $notifiable = $notifiable->routeNotificationForSpark() ? $notifiable->routeNotificationForSpark() : $notifiable;
        }

        $users = get_class($notifiable) === Spark::$teamModel ? $notifiable->users : [$notifiable];

        foreach ($users as $user) {
            parent::send($user, $template);
        }
    }

    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        event(app()->makeWith(SparkNotificationSent::class, [
            'notifiable' => $notification->notifiable,
            'notification' => $notification,
        ]));
    }
}
