<?php

namespace Tests\Repositories;

use Illuminate\Database\Eloquent\Model;
use OhKannaDuh\Repositories\RepositoryInterface;

final class SpyRepository implements RepositoryInterface
{

    public function find($identifier): ?Model
    {
        return null;
    }
}
