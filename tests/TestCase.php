<?php

namespace Aldeebhasan\LaSubscription\Tests;

use Aldeebhasan\LaSubscription\LaSubscriptionServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Aldeebhasan\\LaSubscription\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Sample/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaSubscriptionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        //        config()->set('subscription', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_la-subscription_table.php.stub';
        $migration->up();
        */
    }
}
