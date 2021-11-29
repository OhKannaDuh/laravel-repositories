<?php

namespace OhKannaDuh\Repositories\Rules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use OhKannaDuh\Repositories\Rules\Contracts\ProvidesRules;

final class EloquentRuleProvider implements ProvidesRules
{
    /** @inheritDoc */
    public function provides(): array
    {
        return [
            'exists',
            'unique',
        ];
    }

    /**
     * @param class-string<Model> $model
     * @param string $column
     *
     * @return string[]
     */
    public function exists(string $model, string $column = 'id'): array
    {
        $table = (new $model())->getTable();

        return [
            'exists:' . $table . ',' . $column,
        ];
    }

    /**
     * @param class-string<Model> $model
     * @param string $column
     *
     * @return Unique[]
     */
    public function unique(string $model, string $column = 'NULL'): array
    {
        return [
            new Unique($model, $column),
        ];
    }
}
