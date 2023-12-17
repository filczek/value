<?php

declare(strict_types=1);

namespace Filczek\Value\Array\Exception;

use InvalidArgumentException;

final class ValueIsNotArray extends InvalidArgumentException
{
    public function __construct(string $expected)
    {
        $message = "Value is not $expected array!";

        parent::__construct($message);
    }

    public static function associative(): self
    {
        return new self("associative");
    }

    public static function list(): self
    {
        return new self("a list");
    }
}