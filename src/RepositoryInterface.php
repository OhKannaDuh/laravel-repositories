<?php

namespace OhKannaDuh\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TEntity as Model of Model
 */
interface RepositoryInterface
{
    /**
     * Get all entites in this repository.
     *
     * @param string[] $columns
     *
     * @return Collection<Model>
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Count the number of entities in this repository.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Find an entity by its identifier in this repository.
     *
     * @param int $identifier
     * @param string[] $columns
     *
     * @return Model|null
     */
    public function find(int $identifier, $columns = ['*']): ?Model;
}
