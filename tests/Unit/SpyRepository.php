<?php

namespace Tests\Unit;

use OhKannaDuh\Repositories\BaseRepository;

final class SpyRepository extends BaseRepository
{
    /**
     * @param Spy $spy
     */
    public function __construct(Spy $spy)
    {
        $this->model = $spy;
    }

    /** @inheritDoc */
    protected function getCreateRules(): array
    {
        return [
            'alias'  => 'unique:spies',
        ];
    }

    /** @inheritDoc */
    protected function getUpdateRules(): array
    {
        return [
            'aliase'  => 'unique:spies',
        ];
    }
}
