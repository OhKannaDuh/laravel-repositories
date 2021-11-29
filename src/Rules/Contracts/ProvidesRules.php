<?php

namespace OhKannaDuh\Repositories\Rules\Contracts;

interface ProvidesRules
{
    // /**
    //  * @return string
    //  */
    // public function getKey(): string;

    /**
     * @return string[]
     */
    public function provides(): array;
}
