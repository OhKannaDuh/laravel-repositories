<?php

namespace OhKannaDuh\Repositories;

use Illuminate\Database\Eloquent\Model;
use OhKannaDuh\Repositories\Models\User;

/**
 * @extends AbstractRepsoitory<User>
 */
final class UserRepository extends AbstractRepsoitory
{
    /** @inheritDoc */
    protected function getModel(): Model
    {
        return new User();
    }
}
