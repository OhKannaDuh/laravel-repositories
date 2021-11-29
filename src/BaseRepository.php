<?php

namespace OhKannaDuh\Repositories;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * @template T as Model of Model
 * @implements RepositoryInterface<T>
 */
abstract class BaseRepository implements RepositoryInterface
{
    /** @var Model */
    protected $model;

    /** @var bool */
    protected $cacheEnabled = true;

    /** @var bool */
    protected $missCache = false;

    /** @var bool */
    protected $dontClearCache = false;

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
     * Get the cache ttl in seconds
     *
     * @return int
     */
    protected function getCacheTtl(): int
    {
        return config('repositories.cache.ttl', 0);
    }

    /**
     * @return array<string,string[]>
     */
    protected function getCacheClearConfig(): array
    {
        return config('repositories.cache.clear', []);
    }

    /**
     * @return string[]
     */
    protected function getCachableMethods(): array
    {
        return config('repositories.cache.methods', []);
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    private function shouldClearCache(string $action): bool
    {
        if ($this->dontClearCache || !$this->cacheEnabled) {
            $this->dontClearCache = false;
            return false;
        }

        $config = $this->getCacheClearConfig();
        return array_key_exists($action, $config) && !empty($config[$action]);
    }

    /**
     * @param string $action
     * @param array<string,mixed> $data = []
     *
     * @return void
     */
    private function clearCache(string $action, array $data = []): void
    {
        $config = $this->getCacheClearConfig();
        $caches = array_key_exists($action, $config) ? $config[$action] : [];

        foreach ($caches as $cache) {
            $matches = [];
            $cacheKey = $this->getKeyPrefix() . '.' . $cache;
            if (preg_match_all('/{(.*?)}/', $cacheKey, $matches) !== false) {
                foreach ($matches[0] as $index => $match) {
                    $key = $matches[1][$index];
                    if (!array_key_exists($key, $data)) {
                        continue;
                    }

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
        if ($this->missCache || !$this->cacheEnabled) {
            $this->missCache = false;
            return false;
        }

        return in_array($action, $this->getCachableMethods());
    }

    /** @inheritDoc */
    public function withoutCache(): self
    {
        $this->missCache = true;
        return $this;
    }

    /** @inheritDoc */
    public function dontClearCache(): self
    {
        $this->dontClearCache = true;
        return $this;
    }

    /** @inheritDoc */
    public function disableCache(): self
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /** @inheritDoc */
    public function enableCache(): self
    {
        $this->cacheEnabled = true;
        return $this;
    }

    /**
     * @param string $action
     * @param \Closure $callback
     * @param string|null $key
     * @param array<string,mixed> $data
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
            $ttl = $this->getCacheTtl();

            return $this->getCache()->remember($key, $ttl, $callback);
        }

        return $callback();
    }

    /**
     * Get the validation rules to use when creating this model.
     *
     * @codeCoverageIgnore
     *
     * @return array<string,mixed>
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
     * @return array<string,mixed>
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
        return $this->execute(__FUNCTION__, fn (): Collection => $this->getModel()->all($columns));
    }

    /** @inheritDoc */
    public function count(): int
    {
        return $this->execute(__FUNCTION__, fn (): int => $this->getModel()->count());
    }

    /** @inheritDoc */
    public function create(array $attributes): Model
    {
        $this->getCreateValidator($attributes)->validate();

        return $this->execute(__FUNCTION__, fn (): Model => $this->getModel()->newQuery()->create($attributes));
    }

    /** @inheritDoc */
    public function find($identifier): ?Model
    {
        $key = implode('.', [$this->getKeyPrefix(), __FUNCTION__, $identifier]);
        return $this->execute(__FUNCTION__, fn (): ?Model => $this->getModel()->newQuery()->find($identifier), $key, [
            'identifier' => $identifier,
        ]);
    }

    /** @inheritDoc */
    public function update(Model $model, array $attributes): bool
    {
        $this->getUpdatevalidator($attributes)->validate();

        return $this->execute(__FUNCTION__, fn (): bool => $model->update($attributes), null, [
            'identifier' => $model->getKey(),
        ]);
    }

    /** @inheritDoc */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): Builder
    {
        return $this->execute(
            __FUNCTION__,
            fn (): Builder => $this->getModel()->newQuery()->where($column, $operator, $value, $boolean)
        );
    }

    /** @inheritDoc */
    public function chunk(int $size, Closure $callback): bool
    {
        return $this->execute(__FUNCTION__, fn (): bool => $this->getModel()->newQuery()->chunk($size, $callback));
    }

    /** @inheritDoc */
    public function delete(Model $model)
    {
        return $this->execute(__FUNCTION__, fn () => $model->delete(), null, $model->getAttributes());
    }
}
