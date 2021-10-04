<?php

namespace Tests\Behaviours;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait TracksQueries
{
    private $queries;

    public function bootTracksQueries(): void
    {
        $this->queries = new Collection();
        DB::listen(fn ($query) => $this->queries->add(['query' => $query->sql]));
    }

    /**
     * @param int $count
     *
     * @return void
     */
    public function assertQueryCount(int $count): void
    {
        $this->assertCount($count, $this->queries);
    }

    /**
     * @param int $count
     * @param string $field
     * @param string $value
     *
     * @return void
     */
    public function assertQueryCountWhere(int $count, string $field, string $value): void
    {
        $this->assertCount($count, $this->queries->where($field, $value));
    }
}
