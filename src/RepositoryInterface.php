<?php

namespace OhKannaDuh\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Validation\Validator;

/**
 * @template T as Model of Model
 */
interface RepositoryInterface
{
    /**
     * Get all entitiies for this repository.
     *
     * @param string[] $columns
     *
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;


    /**
     * Count the number of entities in this repository.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Create an entity for this repository.
     *
     * @param array<string,mixed> $attributes
     *
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Find an entity in this repository by its identifier.
     *
     * @param int $identifier
     *
     * @return Model|null
     */
    public function find(int $identifier): ?Model;

    /**
     * Update the given entity in this repository.
     *
     * @param Model $model
     * @param array<string,mixed> $attributes
     *
     * @return bool
     */
    public function update(Model $model, array $attributes): bool;

    /**
     * Find a matching entity in this repository.
     *
     * @param Closure|string|string[]|Expression $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $boolean
     *
     * @return Builder
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): Builder;

    /**
     * @param array<string,mixed> $input
     *
     * @return Validator
     */
    public function getCreateValidator(array $input = []): Validator;

    /**
     * @param array<string,mixed> $input
     *
     * @return Validator
     */
    public function getUpdateValidator(array $input = []): Validator;

    /**
     * Run a query on the repository without using cache.
     *
     * @return self
     */
    public function withoutCache(): self;

    /**
     * Run a query on the repository without clearing the cache.
     *
     * @return self
     */
    public function dontClearCache(): self;

    /**
     * Disable the cache on this repository.
     *
     * @return self
     */
    public function disableCache(): self;

    /**
     * Enable the cache on this repository.
     *
     * @return self
     */
    public function enableCache(): self;

    /**
     * @param int $size
     * @param Closure(Model $model): bool $callback
     *
     * @return bool
     */
    public function chunk(int $size, Closure $callback): bool;

    /**
     * Delete the given model.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function delete(Model $model): bool;
}
