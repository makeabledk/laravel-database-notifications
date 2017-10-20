<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;

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
        // Only set 'reserved_at' first time in case it fails and tries again later
        if ($this->notification->reserved_at === null) {
            $this->notification->update(['reserved_at' => Carbon::now()]);
        }

        $manager->channel($this->notification->channel)->sendNow($this->notification);

        $this->notification->update(['sent_at' => Carbon::now()]);
    }
}
