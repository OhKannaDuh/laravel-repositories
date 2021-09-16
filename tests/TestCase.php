<?php

namespace Tests;

use OhKannaDuh\Repositories\Providers\RepositoryServiceProvider;

abstract class TestCase extends \Orchestra\Canvas\Core\Testing\TestCase
{
    /** @inheritDoc */
    protected function getPackageProviders($app)
    {
        return [RepositoryServiceProvider::class];
    }

    /** @inheritDoc */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
