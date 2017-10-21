<?php

namespace Makeable\DatabaseNotifications\Tests\Feature;

use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Messages\MailMessage;
use Makeable\DatabaseNotifications\Channels\Mail;
use Makeable\DatabaseNotifications\Notification;
use Makeable\DatabaseNotifications\Tests\TestCase;

class StoreNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test * */
    function it_saves_a_notification_to_the_database()
    {
        $this->notifiable()->notify($this->mailNotification());
        $this->assertEquals(1, Notification::count());
    }

    /** @test * */
    function it_stores_with_id_channel_type_and_notifiable()
    {
        $notifiable = $this->notifiable();
        $notification = $this->mailNotification();
        $sent = false;

        Event::listen(NotificationSent::class, function($event) use ($notifiable, &$sent) {
            $stored = Notification::first();

            $this->assertTrue($notifiable->is($stored->notifiable));
            $this->assertEquals($event->notification->id, $stored->id);
            $this->assertEquals('mail', $stored->channel);
            $this->assertEquals(get_class($event->notification), $stored->type);

            $sent = true;
        });

        $notifiable->notify($notification);
        $this->assertTrue($sent);
    }

    /** @test **/
    function it_stores_subject_if_present_on_notification()
    {
//        $notification =
    }

    /** @test **/
    function a_database_notification_instance_can_be_returned_from_a_to_method()
    {
//        $notification =
    }

    /**
     * @return \Illuminate\Notifications\Notification
     */
    protected function mailNotification()
    {
        return new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable){
                return [Mail::class];
            }

            public function toMail($notifiable){
                return (new MailMessage)
                    ->greeting('Hi there')
                    ->line('Your order has been shipped!')
                    ->action('Check it out', 'example.com')
                    ->line('Goodbye!');
            }
        };
    }

}
