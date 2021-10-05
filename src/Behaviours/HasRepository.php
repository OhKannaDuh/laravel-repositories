<?php

namespace OhKannaDuh\Repositories\Behaviours;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use OhKannaDuh\Repositories\Exceptions\NoRepositoryException;
use OhKannaDuh\Repositories\RepositoryInterface;

trait HasRepository
{
    /**
     * @return RepositoryInterface
     *
     * @throws BindingResolutionException
     * @throws NoRepositoryException
     */
    public static function repository(): RepositoryInterface
    {
        $repository = self::newRepository();
        if ($repository instanceof RepositoryInterface) {
            return $repository;
        }

        $class = static::class;
        $prefix = config('repositories.namespaces.model');
        if (Str::startsWith($class, $prefix)) {
            $class = Str::after($class, $prefix);
        }

        $class = config('repositories.namespaces.repository') . $class . 'Repository';

        $repository = App::make($class);
        if (!$repository instanceof RepositoryInterface) {
            throw NoRepositoryException::fromClass(static::class);
        }

        return $repository;
    }

    /**
     * @return void|RepositoryInterface
     */
    protected static function newRepository()
    {
    }
}
