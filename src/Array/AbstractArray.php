<?php

declare(strict_types=1);

namespace Filczek\Value\Array;

use ArrayAccess;
use Closure;
use Countable;
use Filczek\Value\Array\Exception\TryingToModifyImmutableArray;
use Filczek\Value\String\UnicodeString;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * @template TKey
 * @template TValue
 */
abstract class AbstractArray implements ArrayAccess, Countable
{
    protected $array;

    protected function __construct(array $array) {
        $this->array = $array;
    }

    public static function empty(): static
    {
        return static::of([]);
    }

    public static function of($value): static
    {
        return new static((array)$value);
    }

    public function count(): int
    {
        return count($this->array);
    }

    /**
     * @param TKey $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    /**
     * @param TKey $offset
     * @return TValue
     */
    public function offsetGet($offset): mixed
    {
        return $this->array[$offset];
    }

    /**
     * @param TKey $offset
     * @param TValue $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        throw TryingToModifyImmutableArray::of($offset, $value);
    }

    /**
     * @param TKey $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        throw TryingToModifyImmutableArray::ofKey($offset);
    }

    /***
     * @param TKey $key
     * @return TValue|null
     */
    public function at($key)
    {
        return $this->array[$key] ?? null;
    }

    /**
     * Tests if every element passes provided callback function.
     *
     * @param Closure|callable $callback
     * @return bool
     */
    public function every(Closure|callable $callback): bool
    {
        foreach ($this->array as $value) {
            if (false === $callback($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tests whether at least one element passes provided callback function.
     *
     * @param Closure|callable $callback
     * @return bool
     */
    public function some(Closure|callable $callback): bool
    {
        foreach ($this->array as $value) {
            if ($callback($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the elements that match the provided callback.
     *
     * @param Closure|callable|null $callback
     * @return static<TKey, TValue>
     */
    public function filter(Closure|callable|null $callback): static
    {
        $result = array_filter($this->array, $callback, ARRAY_FILTER_USE_BOTH);

        return static::of($result);
    }

    /**
     * Returns the array populated with the result of provided callback function.
     *
     * @template TNewValue
     * @param Closure|callable|null $callback
     * @return static<int, TNewValue>
     */
    public function map(Closure|callable $callback = null): static
    {
        $result = array_map($callback, $this->array);
        return static::of($result);
    }

    /**
     * Find the first element that satisfies the provided callback.
     *
     * @param Closure|callable $callback
     * @return TValue|null
     */
    public function find(Closure|callable $callback)
    {
        $index = $this->findIndex($callback);

        if (false === $index) {
            return null;
        }

        return $this->array[$index];
    }

    /**
     * Find the index of first element that satisfies the provided callback.
     *
     * @param Closure|callable $callback
     * @return TKey|false
     */
    public function findIndex(Closure|callable $callback)
    {
        foreach ($this->array as $index => $item) {
            if ($callback($item, $index)) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Find the last element that satisfies the provided callback.
     *
     * @param Closure|callable $callback
     * @return TValue
     */
    public function findLast(Closure|callable $callback)
    {
        $index = $this->findLastIndex($callback);

        if (false === $index) {
            return null;
        }

        return $this->array[$index];
    }

    /**
     * Find the index of last element that satisfies the provided callback.
     *
     * @param Closure|callable $callback
     * @return TKey|false
     */
    public function findLastIndex(Closure|callable $callback)
    {
        $array = $this->array;
        for (end($array); ($index = key($array)) !== null; prev($array)) {
            $item = current($array);

            if ($callback($item, $index)) {
                return $index;
            }
        }

        return false;
    }

    public function flat(): static
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->array));
        $result = iterator_to_array($iterator, false);

        return static::of($result);
    }

    public function forEach(Closure|callable $callback): void
    {
        foreach ($this->array as $index => $item) {
            $callback($item, $index);
        }
    }

    /**
     * Determine if given item exists in the array.
     *
     * @param TValue $item
     * @return bool
     */
    public function contains($item): bool
    {
        return null !== $this->find(fn ($v) => $v === $item);
    }

    /**
     * Find the index of first element that is equal to given item.
     *
     * @param TValue $item
     * @return TValue|false
     */
    public function indexOf($item)
    {
        return $this->findIndex(fn ($v) => $v === $item);
    }

    /**
     * Find the index of last element that is equal to given item.
     *
     * @param TValue $item
     * @return TValue|false
     */
    public function indexOfLast($item)
    {
        return $this->findLastIndex(fn ($v) => $v === $item);
    }

    /**
     * Concatenate all elements of the array into string.
     *
     * @param string $separator
     * @return UnicodeString
     */
    public function join(string $separator = ","): UnicodeString
    {
        $result = implode($separator, $this->array);
        return UnicodeString::of($result);
    }

    /**
     * Reverses the array.
     *
     * @return $this
     */
    public function reverse(): static
    {
        $result = array_reverse($this->array);
        return static::of($result);
    }

    /**
     * Sorts the array.
     *
     * @param Closure|callable|null $callback
     * @return static<TKey, TValue>
     */
    public function sort(Closure|callable|null $callback = null): static
    {
        $array = $this->array;

        if ($callback) {
            usort($array, $callback);
            return static::of($array);
        }

        sort($array);
        return static::of($array);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return static<TKey, TValue>
     */
    public function splice(int $offset, ?int $length = null): static
    {
        $array = $this->array;
        $array = array_splice($array, $offset, $length);

        return static::of($array);
    }

    /**
     * Split array into chunks.
     *
     * @param int $length
     * @return static<int, static<TKey, TValue>>
     */
    public function chunk(int $length): static
    {
        $chunks = array_chunk($this->array, $length, true);
        return static::of($chunks);
    }

    /**
     * Get the first element of array.
     *
     * @return TValue|null
     */
    public function first()
    {
        $key = array_key_first($this->array);

        return $this->array[$key];
    }

    /**
     * Get the last element of array.
     *
     * @return TValue|null
     */
    public function last()
    {
        $key = array_key_last($this->array);

        return $this->array[$key];
    }

    /**
     * Determine if array is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    /**
     * Determine if array is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return false === $this->array;
    }

    /**
     * Determine if the array is a list.
     *
     * @return bool
     */
    public function isList(): bool
    {
        return array_is_list($this->array);
    }

    /**
     * Determine if the array is associative.
     *
     * @return bool
     */
    public function isAssociative(): bool
    {
        return false === $this->isList();
    }

    /**
     * Get the array keys.
     *
     * @return static<int, TKey>
     */
    public function keys(): static
    {
        $keys = array_keys($this->array);

        return static::of($keys);
    }

    /**
     * Get the array values.
     *
     * @return static<int, TValue>
     */
    public function values(): static
    {
        $values = array_values($this->array);

        return static::of($values);
    }

    /**
     * Get the array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->array;
    }
}