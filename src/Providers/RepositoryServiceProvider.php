<?php

namespace OhKannaDuh\Repositories\Providers;

use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /** @var string */
    private const CONFIG_PATH = __DIR__ . '/../../config/config.php';

    /** @inheritDoc */
    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'repositories');
    }

    /** @inheritDoc */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path('repositories.php'),
            ], 'config');
        }
    }
}
