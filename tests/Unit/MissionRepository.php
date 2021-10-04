<?php

namespace Tests\Unit;

use OhKannaDuh\Repositories\BaseRepository;

final class MissionRepository extends BaseRepository
{
    /**
     * @param Mission $mission
     */
    public function __construct(Mission $mission)
    {
        $this->model = $mission;
    }
}
