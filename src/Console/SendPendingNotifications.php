<?php

namespace Makeable\DatabaseNotifications\Console;

use Illuminate\Console\Command;
use Makeable\DatabaseNotifications\Jobs\SendNotification;
use Makeable\DatabaseNotifications\Notification;

class SendPendingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all pending database notifications';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(Notification::class)
            ->pending()
            ->get()
            ->each(function (Notification $notification) {
                SendNotification::dispatch($notification);

                $this->comment('Started dispatching notification #'.$notification->id);
            });
    }
}
