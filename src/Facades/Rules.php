<?php

namespace OhKannaDuh\Repositories\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;
use OhKannaDuh\Repositories\Rules\BasicRuleProvider;
use OhKannaDuh\Repositories\Rules\DateRuleProvider;
use OhKannaDuh\Repositories\Rules\EloquentRuleProvider;
use OhKannaDuh\Repositories\Rules\NumberRuleProvider;
use OhKannaDuh\Repositories\Rules\RuleContainer;

/**
 * @method static BasicRuleProvider string(int $max = 0): string[]
 * @method static BasicRuleProvider required(): string[]
 * @method static BasicRuleProvider requiredIf(callable|bool $if): RequiredIf[]
 * @method static BasicRuleProvider nullable(): string[]
 * @method static BasicRuleProvider boolean(): string[]
 * @method static BasicRuleProvider in(array $in): In[]
 * @method static BasicRuleProvider inConfig(string $config): In[]
 * @method static DateRuleProvider date(): string[]
 * @method static DateRuleProvider after(string $after): string[]
 * @method static DateRuleProvider before(string $before): string[]
 * @method static EloquentRuleProvider exists(string $model, string $column = 'id'): string[]
 * @method static EloquentRuleProvider exists(class-string<Model> $model, string $column = 'id'): string[]
 * @method static EloquentRuleProvider unique(class-string<Model> $model, string $column = 'NULL'): Unique[]
 * @method static NumberRuleProvider integer(int $digits = 0): string[]
 * @method static NumberRuleProvider unsigned(): string[]
 * @method static NumberRuleProvider unsignedInteger(int $digits = 0): string[]
 */
final class Rules extends Facade
{
    /** @inheritDoc */
    protected static function getFacadeAccessor(): string
    {
        return RuleContainer::class;
    }
}
