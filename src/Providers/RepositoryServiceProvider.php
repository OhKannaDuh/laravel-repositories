<?php

namespace OhKannaDuh\Repositories\Providers;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

        if (config('repositories.autobind.enabled')) {
            $this->autobind();
        }
    }

    /**
     * @return void
     */
    private function autobind(): void
    {
        $repositories = $this->getRepositories();
        foreach ($repositories as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    /**
     * Get the repositories from the cache if enabled.
     *
     * @return iterable
     */
    private function getRepositories(): iterable
    {
        $cache = config('repositories.autobind.cache.enabled');
        $ttl = config('repositories.autobind.cache.ttl');

        return $cache
            ? Cache::store()->remember('repositories-autobind-classes', $ttl, fn () =>  $this->getRepositoryList())
            : $this->getRepositoryList();
    }

    /**
     * Get a list of interface to class repositories.
     *
     * @return iterable
     */
    private function getRepositoryList(): iterable
    {
        $namespace = config('repositories.namespaces.repository') ?? '';
        if (!$namespace) {
            return;
        }

        $repositories = collect(ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE));
        $repositories = $repositories->filter(fn ($value) => !Str::endsWith($value, 'Interface'));
        foreach ($repositories as $class) {
            $interface = $class . 'Interface';
            if (!interface_exists($interface)) {
                continue;
            }

            yield $interface => $class;
        }
    }
}
