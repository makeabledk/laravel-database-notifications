<?php

namespace Makeable\DatabaseNotifications\Tests;

use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Mail;
use Makeable\DatabaseNotifications\DatabaseNotificationsServiceProvider;
use Makeable\DatabaseNotifications\Tests\Stubs\Order;
use Makeable\DatabaseNotifications\Tests\Stubs\OrderShippedNotification;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        Mail::fake();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(DatabaseNotificationsServiceProvider::class);
        $app->afterResolving('migrator', function ($migrator) {
            $migrator->path(__DIR__.'/migrations/');
        });

        return $app;
    }

    /**
     * @return User
     */
    protected function notifiable()
    {
        return tap(new User, function ($user) {
            $user->fill([
                'name' => 'John',
                'email' => 'test@example.com',
                'password' => 'foo',
            ])->save();
        });
    }

    protected function notification($channel = \Makeable\DatabaseNotifications\Channels\MailChannel::class)
    {
        return new OrderShippedNotification(Order::create(), $channel);
    }
}
