<?php

namespace OhKannaDuh\Repositories\Rules;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\RequiredIf;
use OhKannaDuh\Repositories\Rules\Contracts\ProvidesRules;

final class BasicRuleProvider implements ProvidesRules
{
    /** @inheritDoc */
    public function provides(): array
    {
        return [
            'string',
            'required',
            'requiredIf',
            'nullable',
            'boolean',
            'in',
            'inConfig',
        ];
    }

    /**
     * @param int $max
     *
     * @return string|string[]
     */
    public function string(int $max = 0)
    {
        if ($max > 0) {
            return [
                'string',
                'max:' . $max,
            ];
        }

        return ['string'];
    }

    /**
     * @return string[]
     */
    public function required(): array
    {
        return ['required'];
    }

    /**
     * @param callable|bool $if
     *
     * @return RequiredIf[]
     */
    public function requiredIf($if): array
    {
        return [new RequiredIf($if)];
    }

    /**
     * @return string[]
     */
    public function nullable(): array
    {
        return ['nullable'];
    }

    /**
     * @return string[]
     */
    public function boolean(): array
    {
        return ['boolean'];
    }

    /**
     * @param mixed[] $in
     *
     * @return In[]
     */
    public function in(array $in): array
    {
        return [new In($in)];
    }

    /**
     * @param string $config
     *
     * @return In[]
     */
    public function inConfig(string $config): array
    {
        $values = config($config);
        if (!is_array($values)) {
            // :c
        }

        return $this->in($values);
    }
}
