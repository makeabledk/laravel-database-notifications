<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Notification as NotificationTemplate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Makeable\DatabaseNotifications\InstantNotification;
use Makeable\DatabaseNotifications\Message;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

abstract class Channel
{
    /**
     * @param $data
     * @return mixed
     */
    abstract public function deserialize($data);

    /**
     * @return string
     */
    public function driver()
    {
        return Str::camel(class_basename($this));
    }

    /*
    * @param $notifiable
    * @param NotificationTemplate $notification
    */
    public function send($notifiable, NotificationTemplate $notification)
    {
        DatabaseNotification::queue($this
            ->getMessage($notifiable, $notification)
            ->setNotifiable($notifiable)
            ->setType($this->driver())
            ->transformData([$this, 'serialize'])
        );
    }

    public function sendNow(DatabaseNotification $notification)
    {
        app(Dispatcher::class)->sendNow(
            $notification->notifiable,
            new InstantNotification($this->deserialize($notification->data)),
            $notification->type // todo support alias through manager
        );
    }

    /**
     * @param $data
     * @return array
     */
    public function serialize($data)
    {
        if (is_array($data)) {
            return $data;
        }
        return get_object_vars($data);
    }

    /**
     * @return string
     */
    public function toMethod()
    {
        return Str::camel('to'.$this->driver());
    }

    /**
     * @param $notifiable
     * @param NotificationTemplate $notification
     * @return Message
     */
    protected function getMessage($notifiable, NotificationTemplate $notification)
    {
        $message = $notification->{$this->toMethod()}($notifiable);

        if (! $message instanceof Message) {
            $message = new Message($message);
        }

        return $message;
    }

    /**
     * @param $properties
     * @param $object
     * @return mixed
     */
    protected function applyProperties($properties, $object)
    {
        return tap($object, function ($object) use ($properties) {
            foreach($properties as $property => $value) {
                $object->{$property} = $value;
            }
        });
    }
}