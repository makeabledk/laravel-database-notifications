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

        $this->setUpDatabase($this->app);

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

        return $app;
    }

    /**
     * @return User
     */
    protected function notifiable()
    {
        return tap(new User, function($user) {
            $user->fill([
                'name' => 'John',
                'email' => 'test@example.com',
                'password' => 'foo'
            ])->save();
        });
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('orders', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    protected function notification($channel = \Makeable\DatabaseNotifications\Channels\MailChannel::class)
    {
        return new OrderShippedNotification(Order::create(), $channel);
    }
}
