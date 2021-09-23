<?php

namespace OhKannaDuh\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Validator;

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
     * @return Model
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and'): Model;

    /**
     * @param  \Closure|string|array|\Illuminate\Database\Query\Expression $column
     * @param  mixed $operator
     * @param  mixed $value
     * @param  string $boolean
     *
     * @return Collection
     */
    public function allWhere($column, $operator = null, $value = null, $boolean = 'and'): Collection;

    /**
     * @param  \Closure|string|array|\Illuminate\Database\Query\Expression $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     *
     * @return int
     */
    public function countWhere($column, $operator = null, $value = null, $boolean = 'and'): int;

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
}
