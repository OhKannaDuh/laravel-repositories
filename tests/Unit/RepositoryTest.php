<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var SpyRepository|null */
    private $repository;

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();

        config(['repositories::cache.ttl' => 120]);
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
        config(['repositories::cache.methods' => ['all']]);

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
        config(['repositories::cache.methods' => ['all']]);
        config(['repositories::cache.clear.create' => ['all']]);

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

        $mario = $this->repository->where('alias', 'Mario');
        $this->assertEquals(20, $mario->missions_complete);
    }

    /**
     * Ensure we can query a repository to get all entities matching the given criteria.
     */
    public function testAllWhere(): void
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

        $this->assertCount(2, $this->repository->allWhere('missions_complete', '>', '10'));
    }

    /**
     * Ensure we can query a repository to count all entities matching the given criteria.
     */
    public function testCountWhere(): void
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

        $this->assertSame(2, $this->repository->countWhere('missions_complete', '>', '10'));
    }
}
