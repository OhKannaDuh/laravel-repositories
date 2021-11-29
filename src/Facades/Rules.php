<?php

namespace OhKannaDuh\Repositories\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;
use OhKannaDuh\Repositories\Rules\RuleContainer;

/**
 * @method static string[] string(int $max = 0)
 * @method static string[] required()
 * @method static RequiredIf[] requiredIf(callable|bool $if)
 * @method static string[] nullable()
 * @method static string[] boolean()
 * @method static In[] in(array $in)
 * @method static In[] inConfig(string $config)
 * @method static string[] date()
 * @method static string[] after(string $after)
 * @method static string[] before(string $before)
 * @method static string[] exists(string $model, string $column = 'id')
 * @method static string[] exists(class-string<Model> $model, string $column = 'id')
 * @method static Unique[] unique(class-string<Model> $model, string $column = 'NULL')
 * @method static string[] integer(int $digits = 0)
 * @method static string[] unsigned()
 * @method static string[] unsignedInteger(int $digits = 0)
 */
class Rules extends Facade
{
    /** @inheritDoc */
    protected static function getFacadeAccessor(): string
    {
        return RuleContainer::class;
    }
}
