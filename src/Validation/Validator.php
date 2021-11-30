<?php

namespace OhKannaDuh\Repositories\Validation;

use Illuminate\Validation\Validator as IlluminateValidator;

class Validator extends IlluminateValidator
{
    /**
     * @param string $rule
     *
     * @return self
     */
    public function withoutRule(string $rule): self
    {
        $clone = clone $this;
        if (array_key_exists($rule, $clone->rules)) {
            unset($clone->rules[$rule]);
        }

        return $clone;
    }
}
