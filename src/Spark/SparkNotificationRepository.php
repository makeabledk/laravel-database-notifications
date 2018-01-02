<?php

namespace Makeable\DatabaseNotifications\Spark;

use Laravel\Spark\Contracts\Repositories\NotificationRepository as NotificationRepositoryContract;
use Laravel\Spark\Events\NotificationCreated;
use Makeable\DatabaseNotifications\Channels\SparkChannel;
use Makeable\DatabaseNotifications\DatabaseChannelManager;
use Makeable\DatabaseNotifications\Notification;

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
            ->channel($this->sparkChannel())
            ->unread()
            ->latest('sent_at')
            ->get();

        // Retrieve the 8 most recent read notifications for the user...
        $readNotifications = Notification::with('creator')
            ->whereMorph('notifiable', $user)
            ->channel($this->sparkChannel())
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
        $notification->channel = $this->sparkChannel();
        $notification->data = array_only($data, ['icon', 'body', 'action_text', 'action_url']);
        $notification->template = array_get($data, 'template');
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

    /**
     * @return string
     */
    private function sparkChannel()
    {
        return app(DatabaseChannelManager::class)->getAlias(SparkChannel::class);
    }
}
