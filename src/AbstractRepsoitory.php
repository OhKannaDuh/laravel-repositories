<?php

namespace OhKannaDuh\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TEntity as Model of Model
 * @implements RepositoryInterface<TEntity>
 */
abstract class AbstractRepsoitory implements RepositoryInterface
{
    /** @inheritDoc */
    public function all($columns = ['*']): Collection
    {
        return $this->getModel()->all($columns);
    }

    /** @inheritDoc */
    public function count(): int
    {
        return $this->getModel()->count();
    }

    /** @inheritDoc */
    public function find(int $identifier, $columns = ['*']): ?Model
    {
        return $this->getModel()->newQuery()->find($identifier, $columns);
    }

    /**
     * @return Model
     */
    abstract protected function getModel(): Model;
}
