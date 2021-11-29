<?php

namespace OhKannaDuh\Repositories\Providers;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use OhKannaDuh\Repositories\Commands\RepositoryMakeCommand;
use OhKannaDuh\Repositories\Rules\BasicRuleProvider;
use OhKannaDuh\Repositories\Rules\DateRuleProvider;
use OhKannaDuh\Repositories\Rules\EloquentRuleProvider;
use OhKannaDuh\Repositories\Rules\NumberRuleProvider;
use OhKannaDuh\Repositories\Rules\RuleContainer;

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

            $this->commands([
                RepositoryMakeCommand::class,
            ]);
        }

        if (config('repositories.autobind.enabled')) {
            $this->autobind();
        }

        $this->app->singleton(RuleContainer::class, function () {
            $rules = new RuleContainer();
            $rules->register(new BasicRuleProvider());
            $rules->register(new DateRuleProvider());
            $rules->register(new EloquentRuleProvider());
            $rules->register(new NumberRuleProvider());

            return $rules;
        });
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
            ? Cache::store()->remember('repositories-autobound-classes', $ttl, fn () =>  $this->getRepositoryList())
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

        $bind = config('repositories.autobind.bind');
        if (is_array($bind)) {
            $list = array_merge($list, $bind);
        }

        return $list;
    }
}
