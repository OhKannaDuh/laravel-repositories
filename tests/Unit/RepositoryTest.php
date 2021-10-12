<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\Behaviours\TracksQueries;
use Tests\Models\Spy;
use Tests\Repositories\SpyRepository;
use Tests\TestCase;

final class RepositoryTest extends TestCase
{
    use RefreshDatabase;
    use TracksQueries;

    /** @var SpyRepository|null */
    private $repository;

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();

        config(['repositories.cache.ttl' => 120]);
        $this->repository = $this->app->make(SpyRepository::class);
    }

    /**
     * Ensure we can get all entities from a repository.
     */
    public function testAll(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 50,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the first entry
        $this->assertCount(1, $this->repository->all());

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 3,
            'active' => true,
        ]);

        // Ensure we have 2 after creating the second entry
        $this->assertCount(2, $this->repository->all());
    }

    /**
     * Ensure we can cache the results of calls based on the config.
     */
    public function testCache(): void
    {
        config(['repositories.cache.methods' => ['all']]);
        config(['repositories.cache.clear.create' => []]);

        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 50,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the first entry
        $this->assertCount(1, $this->repository->all());

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 3,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the second entry (becaue the  initial call has been cached)
        $this->assertCount(1, $this->repository->all());
    }

    /**
     * Ensure we can clear cache on calls based on config.
     */
    public function testCacheClearing(): void
    {
        config(['repositories.cache.methods' => ['all']]);
        config(['repositories.cache.clear.create' => ['all']]);

        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 50,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the first entry
        $this->assertCount(1, $this->repository->all());

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 3,
            'active' => true,
        ]);

        // Ensure we have 2 after creating the second entry (becaue the  initial call has been cached)
        $this->assertCount(2, $this->repository->all());
    }

    /**
     * Ensure we can correctly count the entities in our repository.
     */
    public function testCount(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 50,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the first entry
        $this->assertEquals(1, $this->repository->count());

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 3,
            'active' => true,
        ]);

        // Ensure we have 2 after creating the second entry
        $this->assertEquals(2, $this->repository->count());
    }

    /**
     * Ensure our repository can create entities.
     */
    public function testCreate(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Johnny English',
            'alias' => 'English',
            'missions_complete' => 2,
            'active' => true,
        ]);

        // Ensure we now have 1
        $this->assertDatabaseCount($table, 1);
        // Ensure it is the entry we expect
        $this->assertDatabaseHas($table, [
            'alias' => 'English',
        ]);
    }

    /**
     * Ensure our repository can't create entites that violate the rules'.
     */
    public function testCreateWithRuleViolation(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid.');

        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Johnny English',
            'alias' => 'English',
            'missions_complete' => 2,
            'active' => true,
        ]);

        $this->repository->create([
            'name' => 'Bonny English',
            // Alias is set to unique in the validator rules
            'alias' => 'English',
            'missions_complete' => 22,
            'active' => false,
        ]);
    }

    /**
     * Ensure we can find an entity by its primary key.
     */
    public function testFind(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $model = $this->repository->create([
            'name' => 'Johnny English',
            'alias' => 'English',
            'missions_complete' => 2,
            'active' => true,
        ]);

        $this->assertTrue($model->is($this->repository->find($model->getKey())));
    }

    /**
     * Ensure we can cache the results of find.
     */
    public function testFindCache(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $modelKey = $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 30,
            'active' => true,
        ])->getKey();

        // This should get and cache the model
        $this->repository->find($modelKey);
        // This should load the model from cache
        $this->repository->find($modelKey);

        // If that worked then we should only be trying to select the result from the db once
        $this->assertQueryCountWhere(1, 'query', 'select * from "spies" where "spies"."id" = ? limit 1');
    }

    /**
     * Ensure we can update an entity in this repository.
     */
    public function testUpdate(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $model = $this->repository->create([
            'name' => 'Johnny English',
            'alias' => 'English',
            'missions_complete' => 2,
            'active' => true,
        ]);

        $this->repository->update($model, [
            'missions_complete' => 3,
        ]);

        $model = $this->repository->find($model->getKey());

        $this->assertEquals(3, $model->missions_complete);
    }

    /**
     * Ensure we can query a repository to get an entity matching the given criteria.
     */
    public function testWhere(): void
    {
        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Johnny English',
            'alias' => 'English',
            'missions_complete' => 2,
            'active' => true,
        ]);

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 20,
            'active' => true,
        ]);

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 30,
            'active' => true,
        ]);

        $mario = $this->repository->where('alias', 'Mario')->first();
        $this->assertEquals(20, $mario->missions_complete);
    }

    /**
     * Ensure we can access a repository witout cache.
     */
    public function testWithoutCache(): void
    {
        $queries = new Collection();
        DB::listen(function ($query) use (&$queries) {
            $queries->add(['query' => $query->sql]);
        });

        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $modelKey = $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 30,
            'active' => true,
        ])->getKey();

        // This should get and cache the model
        $this->repository->find($modelKey);
        // This should also get and cache the model
        $this->repository->withoutCache()->find($modelKey);
        // This should use cache
        $this->repository->find($modelKey);

        // If that worked then we should have hit the database twice
        $this->assertCount(2, $queries->where('query', 'select * from "spies" where "spies"."id" = ? limit 1'));
    }

    /**
     * Ensure we can avoid clearing a cache.
     */
    public function testDontClearCache(): void
    {
        // Cache the results of 'all'
        config(['repositories.cache.methods' => ['all']]);
        // Clear the 'all' cache on 'create'
        config(['repositories.cache.clear.create' => ['all']]);

        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 50,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the first entry
        $this->assertCount(1, $this->repository->all());

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 3,
            'active' => true,
        ]);

        // The cache should have been cleared so we should have 2 entries
        $this->assertCount(2, $this->repository->all());

        // Create a third without clearing the cache
        $this->repository->dontClearCache()->create([
            'name' => 'Waluigi Wario',
            'alias' => 'Waluigi',
            'missions_complete' => 0,
            'active' => true,
        ]);

        // We told the repository not to clear the cache on that create so all should still have 2 entries
        $this->assertCount(2, $this->repository->all());
    }

    /**
     * Ensure we can disabled and enable the cache as needed.
     */
    public function testToggleCache(): void
    {
        // Cache the results of 'all'
        config(['repositories.cache.methods' => ['all']]);
        // Clear the 'all' cache on 'create'
        config(['repositories.cache.clear.create' => ['all']]);

        $table = $this->app->make(Spy::class)->getTable();
        // Ensure we start with no spies in our table.
        $this->assertDatabaseCount($table, 0);

        // Disable the cache
        $this->repository->disableCache();

        $this->repository->create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 50,
            'active' => true,
        ]);

        // Ensure we have 1 after creating the first entry
        $this->assertCount(1, $this->repository->all());

        $this->repository->create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 3,
            'active' => true,
        ]);

        // The cache should have been cleared so we should have 2 entries
        $this->assertCount(2, $this->repository->all());

        // Create a third and ask it not to clear the cache (This shouldn't matter as the cache is disabled anyway)
        $this->repository->dontClearCache()->create([
            'name' => 'Wario Wario',
            'alias' => 'Wario',
            'missions_complete' => 0,
            'active' => true,
        ]);

        // We told the repository not to clear the cache but we have the cache disabled so we should have 3 entries
        $this->assertCount(3, $this->repository->all());

        // Re-enable the cache
        $this->repository->enableCache();

        // Create a third spy, since the cache is now enabled, this should clear the 'all' cache if it exists.
        $this->repository->create([
            'name' => 'Waluigi Wario',
            'alias' => 'Waluigi',
            'missions_complete' => 0,
            'active' => true,
        ]);

        $this->assertCount(4, $this->repository->all());

        // This should avoid clearing the 'all' cache
        $this->repository->dontClearCache()->create([
            'name' => 'Toad',
            'alias' => 'Toad',
            'missions_complete' => 0,
            'active' => true,
        ]);

        $this->assertCount(4, $this->repository->all());
    }
}
