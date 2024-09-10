<?php

namespace BendeckDavid\GraphqlClient\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables;

class TestCase extends BaseTestCase
{

    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config) {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);

            // Setup queue database connections.
            $config([
                'queue.batching.database' => 'testbench',
                'queue.failed.database' => 'testbench',
            ]);
        });
    }

}
