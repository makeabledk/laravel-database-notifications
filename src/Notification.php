<?php

namespace Makeable\DatabaseNotifications;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array'
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

    /**
     * @param $query
     * @return mixed
     */
    public function scopePending($query)
    {
        return $query
            ->where('available_at', '<', Carbon::now())
            ->whereNull('reserved_at')
            ->whereNull('sent_at');
    }
}