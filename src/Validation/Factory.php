<?php

namespace OhKannaDuh\Repositories\Validation;

use Illuminate\Validation\Factory as ValidationFactory;

class Factory extends ValidationFactory
{
    /** @inheritDoc */
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes): Validator
    {
        if (is_null($this->resolver)) {
            $validator = new Validator($this->translator, $data, $rules, $messages, $customAttributes);
            $validator->setPresenceVerifier(app('validation.presence'));

            return $validator;
        }

        return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
    }
}
