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
            ], 'repositories-config');
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
     * @return array<string,string>
     */
    private function getRepositories(): array
    {
        $cache = config('repositories.autobind.cache.enabled');
        $ttl = config('repositories.autobind.cache.ttl');

        return $cache
            ? Cache::store()->remember('repositories-autobind-classes', $ttl, fn () =>  $this->getRepositoryList())
            : $this->getRepositoryList();
    }

    /**
     * Get an array/map of interface to class repositories.
     *
     * @return array<string,string>
     */
    private function getRepositoryList(): array
    {
        $list = [];

        $namespace = config('repositories.namespaces.repository') ?? '';
        if (!$namespace) {
            return $list;
        }

        $repositories = collect(ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE));
        $repositories = $repositories->filter(fn ($value) => !Str::endsWith($value, 'Interface'));
        foreach ($repositories as $class) {
            $interface = $class . 'Interface';
            if (!interface_exists($interface)) {
                continue;
            }

            $list[$interface] = $class;
        }

        return $list;
    }
}
