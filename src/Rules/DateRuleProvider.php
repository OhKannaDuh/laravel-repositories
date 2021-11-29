<?php

namespace OhKannaDuh\Repositories\Rules;

use OhKannaDuh\Repositories\Rules\Contracts\ProvidesRules;

final class DateRuleProvider implements ProvidesRules
{
    /** @inheritDoc */
    public function provides(): array
    {
        return [
            'date',
            'after',
            'before',
        ];
    }

    /**
     * @return string[]
     */
    public function date(): array
    {
        return ['date'];
    }

    /**
     * @param string $after
     *
     * @return string[]
     */
    public function after(string $after): array
    {
        return array_merge(
            $this->date(),
            ['after:' . $after],
        );
    }

    /**
     * @param string $before
     *
     * @return string[]
     */
    public function before(string $before): array
    {
        return array_merge(
            $this->date(),
            ['before:' . $before],
        );
    }
}
