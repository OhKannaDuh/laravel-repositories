<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use OhKannaDuh\Repositories\Behaviours\HasRepository;
use Tests\Repositories\SpyRepository;

final class GoodSpy extends Model
{
    use HasRepository;

    /** @inheritDoc */
    protected static function newRepository()
    {
        return App::make(SpyRepository::class);
    }
}
