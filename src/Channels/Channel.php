<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Makeable\DatabaseNotifications\DatabaseChannelManager;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

abstract class Channel
{
    use Serialization;

    /**
     * @param DatabaseNotification $notification
     */
    abstract public function sendNow(DatabaseNotification $notification);

    /**
     * @return string
     */
    public function alias()
    {
        return app(DatabaseChannelManager::class)->getAlias($this);
    }

    /*
    * @param $notifiable
    * @param Notification $notification
    */
    public function send($notifiable, Notification $template)
    {
        $notification = new DatabaseNotification;
        $notification->setMorph('notifiable', $notifiable);
        $notification->setMorph('creator', $this->fetchCreator($template));
        $notification->setMorph('subject', $this->fetchSubject($template));
        $notification->channel = $this->alias();
        $notification->template = get_class($template);

        $this->fillData($notification, $notifiable, $template)->save();

        return $notification;
    }

    /**
     * @return string
     */
    public function toMethod()
    {
        return Str::camel('to_'.$this->alias());
    }

    /**
     * @param DatabaseNotification $notification
     * @param $notifiable
     * @param $template
     * @return DatabaseNotification
     */
    protected function fillData(DatabaseNotification $notification, $notifiable, $template)
    {
        $data = $template->{$this->toMethod()}($notifiable);

        if ($data instanceof DatabaseNotification) {
            $notification->fill(array_except($data->toArray(), ['data']));
            $data = $data->data;
        }

        return $notification->fill(['data' => $this->serialize($data)]);
    }

    /**
     * @param Notification $notification
     * @return mixed
     */
    protected function fetchCreator(Notification $notification)
    {
        return method_exists($notification, 'creator') ? $notification->creator() : null;
    }

    /**
     * @param Notification $notification
     * @return mixed
     */
    protected function fetchSubject(Notification $notification)
    {
        return method_exists($notification, 'subject') ? $notification->subject() : null;
    }
}
