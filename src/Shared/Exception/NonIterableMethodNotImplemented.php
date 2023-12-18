<?php

declare(strict_types=1);

namespace Filczek\Value\Shared\Exception;

use TypeError;

final class NonIterableMethodNotImplemented extends TypeError
{
    private function __construct(string $method, string $class)
    {
        $message = sprintf("Method '%s' needs to be overridden by class '%s' to deal with non-iterable values.", $method, $class);

        parent::__construct($message);
    }

    public static function of(string $method, string $class): self
    {
        return new self($method, $class);
    }
}