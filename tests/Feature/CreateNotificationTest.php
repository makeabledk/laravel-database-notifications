<?php

namespace Makeable\DatabaseNotifications\Tests\Feature;

use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSent;
use Makeable\DatabaseNotifications\Channels\Database;
use Makeable\DatabaseNotifications\Notification;
use Makeable\DatabaseNotifications\Tests\Stubs\OrderShippedNotification;
use Makeable\DatabaseNotifications\Tests\TestCase;

class CreateNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test * */
    function it_stores_a_notification_to_the_database()
    {
        $this->notifiable()->notify($this->notification());
        $this->assertEquals(1, Notification::count());
    }

    /** @test * */
    function it_stores_with_id_channel_type_and_notifiable()
    {
        $notifiable = $this->notifiable();
        $notification = $this->notification();
        $sent = false;

        Event::listen(NotificationSent::class, function($event) use ($notifiable, &$sent) {
            $stored = Notification::first();

            $this->assertTrue($notifiable->is($stored->notifiable));
            $this->assertEquals($event->notification->id, $stored->id);
            $this->assertEquals('mail', $stored->channel);

            $sent = true;
        });

        $notifiable->notify($notification);
        $this->assertTrue($sent);
    }

    /** @test **/
    function it_stores_subject_if_present_on_notification()
    {
        $this->notifiable()->notify($this->notification());
        $database = Notification::first();

        $this->assertEquals('mail', $database->channel);
        $this->assertEquals(1, $database->subject->id);
    }

    /** @test **/
    function it_has_a_notifiable_relationship()
    {
        $notifiable = $this->notifiable();
        $notifiable->notify($this->notification());

        $this->assertTrue($notifiable->is(Notification::first()->notifiable));
    }

    /** @test **/
    function a_database_notification_instance_can_be_returned_from_a_to_method()
    {
        $notifiable = $this->notifiable();
        $notifiable->notify($this->notification(Database::class));
        $database = Notification::first();

        $this->assertEquals('database', $database->channel);
        $this->assertEquals(OrderShippedNotification::class, $database->type);
        $this->assertNotNull($database->available_at);
        $this->assertEquals('Hi there. Your order has been shipped.', $database->data['contents']);
    }
}
