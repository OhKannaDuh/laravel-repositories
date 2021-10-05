<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use OhKannaDuh\Repositories\Behaviours\HasRepository;

final class Spy extends Model
{
    use HasRepository;

    /** @inheritDoc */
    protected $fillable = [
        'name',
        'alias',
        'missions_complete',
        'active',
    ];

    /** @inheritDoc */
    protected $casts = [
        'active' => 'boolean',
    ];

    /** @inheritDoc */
    public $timestamps = false;
}
