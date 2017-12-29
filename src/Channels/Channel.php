<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Makeable\DatabaseNotifications\DatabaseChannelManager;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

abstract class Channel
{
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

        $notification->fill($this->fetchAttributes($notifiable, $template));
        $notification->data = $this->serialize($notification->data);

        $notification->save();

        return $notification;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function serialize($data)
    {
        if (is_object($data)) {
            return get_object_vars($data);
        }

        if (! is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $data[$key] = $this->serialize($value);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function toMethod()
    {
        return Str::camel('to_'.$this->alias());
    }

    /**
     * @param $properties
     * @param $object
     * @return mixed
     */
    protected function buildObject($object, $properties)
    {
        return tap($object, function ($object) use ($properties) {
            foreach ($properties as $property => $value) {
                $object->{$property} = $value;
            }
        });
    }

    /**
     * @param $notifiable
     * @param Notification $notification
     * @return array
     */
    protected function fetchAttributes($notifiable, Notification $notification)
    {
        $data = $notification->{$this->toMethod()}($notifiable);

        if ($data instanceof DatabaseNotification) {
            return $data->toArray();
        }

        return ['data' => $data];
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
