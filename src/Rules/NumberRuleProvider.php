<?php

namespace OhKannaDuh\Repositories\Rules;

use OhKannaDuh\Repositories\Rules\Contracts\ProvidesRules;

final class NumberRuleProvider implements ProvidesRules
{
    /** @inheritDoc */
    public function provides(): array
    {
        return [
            'integer',
            'unsigned',
            'unsignedInteger'
        ];
    }

    /**
     * @param int $digits
     *
     * @return string[]
     */
    public function integer(int $digits = 0)
    {
        if ($digits > 0) {
            return [
                'integer',
                'digits:' . $digits,
            ];
        }

        return ['integer'];
    }

    /**
     * @return string[]
     */
    public function unsigned(): array
    {
        return ['gte:0'];
    }

    /**
     * @param int $digits
     *
     * @return string[]
     */
    public function unsignedInteger(int $digits = 0): array
    {
        return array_merge(
            $this->integer($digits),
            $this->unsigned(),
        );
    }
}
