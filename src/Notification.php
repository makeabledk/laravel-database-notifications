<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use InteractsWithPolymorphism;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'available_at',
        'reserved_at',
        'sent_at',
        'read_at',
        'created_at',
        'updated_at',
    ];

    // _________________________________________________________________________________________________________________

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function creator()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $query
     * @param $channel
     * @return mixed
     */
    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePending($query)
    {
        return $query
            ->where('available_at', '<=', now())
            ->whereNull('reserved_at')
            ->whereNull('sent_at');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // _________________________________________________________________________________________________________________
}
