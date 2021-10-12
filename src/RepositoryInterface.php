<?php

namespace OhKannaDuh\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

interface RepositoryInterface
{
    /**
     * Get all entitiies for this repository.
     *
     * @param array $columns
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
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Find an entity in this repository by its identifier.
     *
     * @param string|int $identifier
     *
     * @return Model|null
     */
    public function find($identifier): ?Model;

    /**
     * Update the given entity in this repository.
     *
     * @return bool
     */
    public function update(Model $model, array $attributes): bool;

    /**
     * Find a matching entity in this repository.
     *
     * @param  \Closure|string|array|\Illuminate\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     *
     * @return Builder
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): Builder;

    /**
     * @param array $input
     *
     * @return Validator
     */
    public function getCreateValidator(array $input = []): Validator;

    /**
     * @param array $input
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
     * @param Closure $callback
     *
     * @return void
     */
    public function chunk(int $size, Closure $callback = null): void;
}
