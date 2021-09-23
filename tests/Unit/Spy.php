<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;

final class Spy extends Model
{
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