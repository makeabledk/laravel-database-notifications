<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Notification;
use Laravel\Spark\Team;
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

        $users = $notifiable instanceof Team ? $notifiable->users : [$notifiable];

        foreach ($users as $user) {
            parent::send($user, $template);
        }
    }

    /**
     * @param DatabaseNotification $notification
     */
    public function sendNow(DatabaseNotification $notification)
    {
        SparkNotificationSent::dispatch($notification->notifiable, $notification);
    }
}