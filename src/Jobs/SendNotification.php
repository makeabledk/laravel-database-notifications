<?php

namespace Makeable\DatabaseNotifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Makeable\DatabaseNotifications\DatabaseChannelManager;
use Makeable\DatabaseNotifications\Notification;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Notification
     */
    public $notification;

    /**
     * Create a new job instance.
     *
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @param DatabaseChannelManager $manager
     * @return void
     */
    public function handle(DatabaseChannelManager $manager)
    {
        if ($this->notification->available_at !== null &&
            $this->notification->available_at->gt(now())) {
            return;
        }

        // Only set 'reserved_at' first time in case it fails and tries again later
        if ($this->notification->reserved_at === null) {
            $this->notification->update(['reserved_at' => Carbon::now()]);
        }

        $manager->channel($this->notification->channel)->sendNow($this->notification);

        $this->notification->update(['sent_at' => Carbon::now()]);
    }
}
