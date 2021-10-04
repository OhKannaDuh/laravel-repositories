<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;

final class Mission extends Model
{
    /** @inheritDoc */
    protected $fillable = [
        'spy_id',
        'name',
        'complete',
    ];

    /** @inheritDoc */
    protected $casts = [
        'complete' => 'boolean',
    ];

    /** @inheritDoc */
    public $timestamps = false;
}
