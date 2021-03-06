<?php

namespace Makeable\DatabaseNotifications\Tests\Stubs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Makeable\DatabaseNotifications\Notification as DatabaseNotification;

class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Order
     */
    protected $order = null;

    /**
     * @var null
     */
    protected $channel = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $channel)
    {
        $this->order = $order;
        $this->channel = $channel;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [$this->channel];
    }

    /**
     * @return Order
     */
    public function subject()
    {
        return $this->order;
    }

    /**
     * @return User
     */
    public function creator()
    {
        return factory(User::class)->create(['id' => 10]);
    }

    /**
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->markdown('tests::order_mail', ['order' => $this->order, 'products' => collect(), 'foo' => 'bar'])
            ->greeting('Hi there')
            ->line('Your order has been shipped!')
            ->action('Check it out', 'example.com')
            ->line('Goodbye!');
    }

    /**
     * @param $notifiable
     * @return DatabaseNotification
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseNotification([
            'data' => [
                'contents' => 'Hi there. Your order has been shipped.',
            ],
            'available_at' => now()->addHour(),
        ]);
    }
}
