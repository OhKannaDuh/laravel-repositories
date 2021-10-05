<?php

namespace Tests\Unit\Behaviours;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OhKannaDuh\Repositories\Exceptions\NoRepositoryException;
use Tests\TestCase;
use Tests\Models\BadSpy;
use Tests\Models\GoodSpy;
use Tests\Models\Spy;
use Tests\Repositories\SpyRepository;

final class HasRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure an exception is thrown if a repository doesn't exist.
     */
    public function testExceptionIsThrownForNonExistingRepository(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->expectExceptionMessage('Target class [App\Repositories\Tests\Models\SpyRepository] does not exist.');

        Spy::repository();
    }

    /**
     * Ensure an exception is thrown if the repository doesn't implmeent the interface
     */
    public function testExceptionIsThrownForNonCompliantObject(): void
    {
        config([
            'repositories.namespaces' => [
                'repository' => 'Tests\\Repositories\\',
                'model' => 'Tests\\Models\\',
            ],
        ]);

        $this->expectException(NoRepositoryException::class);
        $this->expectExceptionMessage('Cannot find a repository for class: Tests\Models\BadSpy');

        BadSpy::repository();
    }

    /**
     * Ensure we can locate the repository for a model.
     */
    public function testWeCanGetTheRepository(): void
    {
        config([
            'repositories.namespaces' => [
                'repository' => 'Tests\\Repositories\\',
                'model' => 'Tests\\Models\\',
            ],
        ]);

        $this->assertInstanceOf(SpyRepository::class, Spy::repository());
    }

    /**
     * Ensure we get the repository if we implement the 'newRepository' static method.
     */
    public function testNewRepositoryOverride(): void
    {
        $this->assertInstanceOf(SpyRepository::class, GoodSpy::repository());
    }
}
