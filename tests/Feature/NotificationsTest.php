<?php

namespace Makeable\DatabaseNotifications\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationManager;
use Makeable\DatabaseNotifications\Notification;
use Makeable\DatabaseNotifications\Tests\Stubs\Order;
use Makeable\DatabaseNotifications\Tests\Stubs\OrderShippedNotification;
use Makeable\DatabaseNotifications\Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    function setUp()
    {
        NotificationManager::fake();
        parent::setUp();
    }

    /** @test * */
    function it_saves_a_notification_to_the_database()
    {
        $this->notifiable()->notify(new OrderShippedNotification());
        $this->assertEquals(1, Notification::count());
    }

    /** @test * */
    function no_notifications_are_sent_automatically()
    {
        $this->notifiable()->notify(new OrderShippedNotification());
        NotificationManager::assertNothingSent();
    }
}
