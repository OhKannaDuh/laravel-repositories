<?php

namespace OhKannaDuh\Repositories\Exceptions;

use RuntimeException;

final class NoRepositoryException extends RuntimeException
{
    /**
     * @param object|string $class
     *
     * @return self
     */
    public static function fromClass($class): self
    {
        $class = is_object($class) ? get_class($class) : $class;

        return new self('Cannot find a repository for class: ' . $class);
    }
}
