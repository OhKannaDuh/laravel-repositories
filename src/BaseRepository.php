<?php

namespace OhKannaDuh\Repositories;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

abstract class BaseRepository implements RepositoryInterface
{
    /** @var Model */
    protected $model;

    /**
     * Get the model for this repository.
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get the unique prefix for this cache;
     *
     * @return string
     */
    protected function getKeyPrefix(): string
    {
        return $this->getModel()->getTable();
    }

    /**
     * @return Repository
     */
    protected function getCache(): Repository
    {
        return Cache::store();
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    private function shouldClearCache(string $action): bool
    {
        return !empty(config('repositories.cache.clear.' . $action) ?? []);
    }

    /**
     * @param string $action
     *
     * @return void
     */
    private function clearCache(string $action, array $data = []): void
    {
        $caches = config('repositories.cache.clear.' . $action) ?? [];
        foreach ($caches as $cache) {
            $matches = [];
            $cacheKey = $this->getKeyPrefix() . '.' . $cache;
            if (preg_match_all('/{(.*?)}/', $cacheKey, $matches) !== false) {
                foreach ($matches[0] as $index => $match) {
                    $key = $matches[1][$index];
                    $cacheKey = Str::replace($match, $data[$key], $cacheKey);
                }
            }

            $this->getCache()->forget($cacheKey);
        }
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    protected function shouldCache(string $action): bool
    {
        return in_array($action, config('repositories.cache.methods') ?? []);
    }

    /**
     * @param string $action
     * @param \Closure $callback
     *
     * @return mixed
     */
    protected function execute(string $action, \Closure $callback, string $key = null, array $data = [])
    {
        if ($key === null) {
            $key = $this->getKeyPrefix() . '.'  . $action;
        }

        if ($this->shouldClearCache($action)) {
            $this->clearCache($action, $data);
        }

        if ($this->shouldCache($action)) {
            $ttl = config('repositories.cache.ttl');

            return $this->getCache()->remember($key, $ttl, $callback);
        }

        return $callback();
    }

    /**
     * Get the validation rules to use when creating this model.
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    protected function getCreateRules(): array
    {
        return [];
    }

    /** @inheritDoc */
    public function getCreateValidator(array $input = []): Validator
    {
        return ValidatorFactory::make($input, $this->getCreateRules());
    }

    /**
     * Get the validation rules to use when updating this model.
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    protected function getUpdateRules(): array
    {
        return [];
    }

    /** @inheritDoc */
    public function getUpdateValidator(array $input = []): Validator
    {
        return ValidatorFactory::make($input, $this->getUpdateRules());
    }

    /** @inheritDoc */
    public function all($columns = ['*']): Collection
    {
        return $this->execute(__FUNCTION__, fn () => $this->getModel()->all($columns));
    }

    /** @inheritDoc */
    public function count(): int
    {
        return $this->execute(__FUNCTION__, fn () => $this->getModel()->count());
    }

    /** @inheritDoc */
    public function create(array $attributes): Model
    {
        $this->getCreateValidator($attributes)->validate();

        return $this->execute(__FUNCTION__, fn () => $this->getModel()->create($attributes));
    }

    /** @inheritDoc */
    public function find($identifier): ?Model
    {
        $key = implode('.', [$this->getKeyPrefix(), __FUNCTION__, $identifier]);
        return $this->execute(__FUNCTION__, fn () => $this->getModel()->find($identifier), $key, [
            'identifier' => $identifier,
        ]);
    }

    /** @inheritDoc */
    public function update(Model $model, array $attributes): bool
    {
        $this->getUpdatevalidator($attributes)->validate();

        return $this->execute(__FUNCTION__, fn () => $model->update($attributes), null, [
            'identifier' => $model->getKey(),
        ]);
    }

    /** @inheritDoc */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): Model
    {
        return $this->execute(
            __FUNCTION__,
            fn () => $this->getModel()->where($column, $operator, $value, $boolean)->first()
        );
    }

    /** @inheritDoc */
    public function allWhere($column, $operator = null, $value = null, $boolean = 'and'): Collection
    {
        return $this->execute(
            __FUNCTION__,
            fn () => $this->getModel()->where($column, $operator, $value, $boolean)->get()
        );
    }

    /** @inheritDoc */
    public function countWhere($column, $operator = null, $value = null, $boolean = 'and'): int
    {
        return $this->execute(
            __FUNCTION__,
            fn () => $this->getModel()->where($column, $operator, $value, $boolean)->count()
        );
    }
}
