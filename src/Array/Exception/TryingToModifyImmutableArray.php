<?php

declare(strict_types=1);

namespace Filczek\Value\Array\Exception;


use BadMethodCallException;

final class TryingToModifyImmutableArray extends BadMethodCallException
{
    public function __construct(string $key, mixed $value)
    {
        if ($value === null) {
            $message = sprintf("Trying to modify the '%s' key in immutable array.", $key);
        } else {
            $message = sprintf("Trying to modify the '%s' key to the '%s' value in immutable array.", $key, $value);
        }

        parent::__construct($message);
    }

    public static function of(mixed $key, mixed $value): self
    {
        return new self((string)$key, $value);
    }

    public static function ofKey(mixed $key): self
    {
        return new self((string)$key, null);
    }
}