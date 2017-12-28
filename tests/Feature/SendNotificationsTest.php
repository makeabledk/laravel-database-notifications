<?php

namespace Makeable\DatabaseNotifications\Tests\Feature;

use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\DatabaseNotifications\Channels\Database;
use Makeable\DatabaseNotifications\Events\MailNotificationSent;
use Makeable\DatabaseNotifications\Notification;
use Makeable\DatabaseNotifications\Tests\TestCase;

class SendNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    function it_sends_queued_notifications_immediately()
    {
        $this->notifiable()->notify($this->notification());

        $notification = Notification::first();

        $this->assertNotNull($notification->reserved_at);
        $this->assertNotNull($notification->sent_at);
    }

    /** @test **/
    function it_does_not_send_queued_notifications_when_switched_off()
    {
        config()->set('database-notifications.send-immediately', false);

        $this->notifiable()->notify($this->notification());

        $notification = Notification::first();

        $this->assertNull($notification->reserved_at);
        $this->assertNull($notification->sent_at);

        // switch back on
        config()->set('database-notifications.send-immediately', true);
    }

    /** @test **/
    function it_does_not_send_notifications_that_are_unavailable()
    {
        // Database notifications are set to have a future available_at in OrderShippedNotification
        $this->notifiable()->notify($this->notification(Database::class));

        $notification = Notification::first();

        $this->assertNull($notification->reserved_at);
        $this->assertNull($notification->sent_at);
    }

    /** @test **/
    function it_can_send_pending_notifications_through_console()
    {
        // Database notifications are set to have a future available_at in OrderShippedNotification
        $this->notifiable()->notify($this->notification(Database::class));
        $this->artisan('notifications:send');

        $notification = Notification::first();

        $this->assertNull($notification->reserved_at);
        $this->assertNull($notification->sent_at);

        $notification->update(['available_at' => now()]);
        $this->artisan('notifications:send');

        $notification->refresh();
        $this->assertNotNull($notification->reserved_at);
        $this->assertNotNull($notification->sent_at);
    }

    /** @test **/
    function it_raises_event_when_notification_was_sent()
    {
        $checkNotifiable = null;
        $checkNotification = null;

        \Event::listen(MailNotificationSent::class, function ($event) use (&$checkNotifiable, &$checkNotification) {
            $checkNotifiable = $event->notifiable;
            $checkNotification = $event->notification;
        });

        $notifiable = $this->notifiable();
        $notifiable->notify($this->notification());

        $this->assertEquals($notifiable->id, $checkNotifiable->id);
        $this->assertEquals(Notification::first()->id, $checkNotification->id);
    }
}
