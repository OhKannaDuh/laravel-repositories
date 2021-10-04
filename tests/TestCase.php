<?php

namespace Tests;

use OhKannaDuh\Repositories\Providers\RepositoryServiceProvider;
use Tests\Behaviours\TracksQueries;

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

    /** @inheritDoc */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[TracksQueries::class])) {
            $this->bootTracksQueries();
        }

        return $uses;
    }
}
