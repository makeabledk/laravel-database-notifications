<?php

namespace Makeable\DatabaseNotifications\Spark;

use Ramsey\Uuid\Uuid;
use Makeable\DatabaseNotifications\Notification;
use Laravel\Spark\Events\NotificationCreated;
use Laravel\Spark\Contracts\Repositories\NotificationRepository as NotificationRepositoryContract;

class SparkNotificationRepository implements NotificationRepositoryContract
{
    /**
     * {@inheritdoc}
     */
    public function recent($user)
    {
        // Retrieve all unread notifications for the user...
        $unreadNotifications = Notification::with('creator')
            ->whereMorph('notifiable', $user)
            ->unread()
            ->latest('sent_at')
            ->get();

        // Retrieve the 8 most recent read notifications for the user...
        $readNotifications = Notification::with('creator')
            ->whereMorph('notifiable', $user)
            ->read()
            ->latest('sent_at')
            ->take(8)
            ->get();

        // Add the read notifications to the unread notifications so they show afterwards...
        $notifications = $unreadNotifications->merge($readNotifications)->sortByDesc('sent_at');

        return $notifications->values();
    }

    /**
     * {@inheritdoc}
     */
    public function create($user, array $data)
    {
        $notification = new Notification();
        $notification->id = Uuid::uuid4();
        $notification->data = array_only($data, ['icon', 'body', 'action_text', 'action_url']);
        $notification->available_at = array_get($data, 'available_at');
        $notification->setMorph('notifiable', $user);
        $notification->setMorph('creator', array_get($data, 'from'));
        $notification->setMorph('subject', array_get($data, 'subject'));
        $notification->save();

        event(new NotificationCreated($notification));

        return $notification;
    }

    /**
     * {@inheritdoc}
     */
    public function personal($user, $from, array $data)
    {
        return $this->create($user, array_merge($data, ['from' => $from]));
    }
}
