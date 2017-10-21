<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Notification as NotificationRecipe;
use Illuminate\Support\Str;
use Makeable\DatabaseNotifications\DatabaseChannelManager;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

abstract class Channel
{
    /**
     * @param $data
     * @return mixed
     */
    abstract public function deserialize($data);

    /**
     * @param DatabaseNotification $notification
     * @return mixed
     * @internal param $data
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
    * @param NotificationRecipe $notification
    */
    public function send($notifiable, NotificationRecipe $recipe)
    {
        $notification = new DatabaseNotification;
        $notification->fill([
            'channel' => $this->alias(),
            'type' => get_class($recipe),
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'subject_type' => optional($this->fetchSubject($recipe))->getMorphClass(),
            'subject_id' => optional($this->fetchSubject($recipe))->getKey(),
        ]);
        $notification->fill($this->fetchAttributes($notifiable, $recipe));
        $notification->data = $this->serialize($notification->data);
        $notification->id = $recipe->id;
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
            foreach($properties as $property => $value) {
                $object->{$property} = $value;
            }
        });
    }

    /**
     * @param $notifiable
     * @param NotificationRecipe $notification
     * @return array
     */
    protected function fetchAttributes($notifiable, NotificationRecipe $notification)
    {
        $data = $notification->{$this->toMethod()}($notifiable);

        if ($data instanceof DatabaseNotification) {
            return $data->toArray();
        }

        return ['data' => $data];
    }

    /**
     * @param NotificationRecipe $notification
     * @return mixed
     */
    protected function fetchSubject(NotificationRecipe $notification)
    {
        return method_exists($notification, 'subject')? $notification->subject() : null;
    }
}