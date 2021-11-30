<?php

namespace Tests\Repositories;

use OhKannaDuh\Repositories\BaseRepository;
use Tests\Models\Spy;

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
    protected function getCreateRules(array $input): array
    {
        return [
            'alias'  => 'unique:spies',
        ];
    }

    /** @inheritDoc */
    protected function getUpdateRules(array $input): array
    {
        return [
            'aliase'  => 'unique:spies',
        ];
    }
}
