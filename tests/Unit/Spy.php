<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return HasMany
     */
    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }
}
