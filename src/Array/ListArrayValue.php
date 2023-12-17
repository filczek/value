<?php

declare(strict_types=1);

namespace Filczek\Value\Array;

use Filczek\Value\Array\Exception\ValueIsNotArray;
use InvalidArgumentException;
use Stringable;

/**
 * @template TValue
 *
 * @extends AbstractArray<int, TValue>
 */
class ListArrayValue extends ArrayValue
{
    public static function of($value): static
    {
        if ($value instanceof Stringable) {
            return new static([(string)$value]);
        }

        if ($value instanceof ListArrayValue) {
            return new static($value->toArray());
        }

        if ($value instanceof AbstractArray && $value->isAssociative()) {
            throw ValueIsNotArray::list();
        }

        if (false === is_iterable($value)) {
            $value = (array)$value;
        }

        if (false === array_is_list($value)) {
            throw ValueIsNotArray::list();
        }

        if (false === is_array($value)) {
            throw ValueIsNotArray::list();
        }

        return new static($value);
    }
}