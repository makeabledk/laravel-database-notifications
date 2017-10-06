<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Notification extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array'
    ];

    /**
     * @param Message $message
     * @return mixed
     */
    public static function queue(Message $message)
    {
        return static::forceCreate([
            'id' => Uuid::uuid4(),
            'type' => $message->getType(),
            'data' => $message->getData(),
            'notifiable_type' => $message->getNotifiable()->getMorphClass(),
            'notifiable_id' => $message->getNotifiable()->getKey()
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}