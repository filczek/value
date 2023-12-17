<?php

declare(strict_types=1);

namespace Filczek\Value\String;

use Closure;
use Filczek\Value\String\Exception\NonIterableMethodNotImplemented;
use Stringable;

abstract class AbstractString implements Stringable
{
    protected $string;

    protected function __construct(string $string) {
        $this->string = $string;
    }

    public static function empty(): static
    {
        return static::of('');
    }

    public static function of($string): static
    {
        return new static((string)$string);
    }

    /**
     * Determine if a string is equal to any given string.
     *
     * @param string|iterable|static $string
     * @return bool
     */
    public function equals(string|iterable|AbstractString $string): bool
    {
        if (false === is_iterable($string)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        foreach ($string as $value) {
            if ($this->equals($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a string is not equal to any given string.
     *
     * @param string|iterable|static $string
     * @return bool
     */
    public function notEquals(string|iterable|AbstractString $string): bool
    {
        return false === $this->equals($string);
    }

    /**
     * Remove all whitespaces from the string.
     *
     * @return static
     */
    public function squish(): static
    {
        return $this
            ->trim()
            ->replaceMatches('~(\s|\x{3164}|\x{1160})+~u', ' ');
    }

    /**
     * Strip whitespace (or other characters) from the beginning and end of a string.
     *
     * @return static
     */
    abstract public function trim(): static;

    /**
     * Strip whitespace (or other characters) from the beginning of a string.
     *
     * @return static
     */
    abstract public function trimStart(): static;

    /**
     * Strip whitespace (or other characters) from the end of a string.
     *
     * @return static
     */
    abstract public function trimEnd(): static;

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string|static $search
     * @return static
     */
    public function before(string|AbstractString $search): static
    {
        $search = static::of($search);

        if ($search->isEmpty()) {
            return static::of($this);
        }

        $index = $this->indexOf($search);

        if (false === $index) {
            return static::of($this);
        }

        return $this->substr(0, $index);
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string|static $search
     * @return static
     */
    public function beforeLast(string|AbstractString $search): static
    {
        $search = static::of($search);

        if ($search->isEmpty()) {
            return static::of($this);
        }

        $position = $this->indexOfLast($search);

        if (false === $position) {
            return static::of($this);
        }

        return $this->substr(0, $position);
    }

    /**
     * Get the portion of a string between two given values.
     *
     * @param string|static $from
     * @param string|static $to
     * @return static
     */
    public function between(string|AbstractString $from, string|AbstractString $to): static
    {
        $from = static::of($from);
        $to = static::of($to);

        if ($from->isEmpty() || $to->isEmpty()) {
            return static::of($this);
        }

        return $this
            ->after($from)
            ->beforeLast($to);
    }

    /**
     * Get the character at the specified index.
     *
     * @param int $index
     * @return static|false
     */
    abstract public function charAt(int $index);

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string|static $search
     * @return static
     */
    public function after(string|AbstractString $search): static
    {
        $search = static::of($search);

        if ($search->isEmpty()) {
            return static::of($this);
        }

        $index = $this->indexOf($search);

        if (false === $index) {
            return static::of($this);
        }

        return $this->substr($index + $search->length());
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string|static $search
     * @return static
     */
    public function afterLast(string|AbstractString $search): static
    {
        $search = static::of($search);

        if ($search->isEmpty()) {
            return static::of($this);
        }

        $position = $this->indexOfLast($search);

        if (false === $position) {
            return static::of($this);
        }

        return $this->substr($position + $search->length());
    }

    /**
     * Returns the portion of the string specified by the start and length parameters.
     *
     * @param int $start
     * @param int|null $length
     * @param string $encoding
     * @return static
     */
    abstract public function substr(int $start, ?int $length = null, string $encoding = 'UTF-8'): static;

    /**
     * Find the multibyte safe position of the first occurrence of a given substring in a string.
     *
     * @param string|static $needle
     * @param int $offset
     * @param string|null $encoding
     * @return int|false
     */
    abstract public function indexOf(string|AbstractString $needle, int $offset = 0, ?string $encoding = null): int|false;

    /**
     * Find the multibyte safe position of the last occurrence of a given substring in a string.
     *
     * @param string|static $needle
     * @param int $offset
     * @param string|null $encoding
     * @return int|false
     */
    abstract public function indexOfLast(string|AbstractString $needle, int $offset = 0, ?string $encoding = null): int|false;

    /**
     * Get reversed string.
     *
     * @return $this
     */
    abstract public function reverse(): static;

    /**
     * Get a string length.
     *
     * @return number
     */
    abstract public function length(): int;

    /**
     * Determine if a string is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return 0 === $this->length();
    }

    /**
     * Determine if a string is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return false === $this->isEmpty();
    }

    /**
     * Determine if a string is 7-bit ASCII.
     *
     * @return bool
     */
    public function isAscii(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return false === $this->matches('/[^\x09\x10\x13\x0A\x0D\x20-\x7E]/');
    }

    /**
     * Transliterate a string to ASCII.
     *
     * @return static
     */
    abstract public function toAscii(): static;

    /**
     * Determine if a string is valid JSON.
     *
     * @return bool
     */
    abstract public function isJson(): bool;

    /**
     * Determine if a string contains any given substring.
     *
     * @param string|iterable|static $needles
     * @param bool $ignore_case
     * @return bool
     */
    public function contains(string|iterable|AbstractString $needles, bool $ignore_case = false): bool
    {
        if (false === is_iterable($needles)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        foreach ($needles as $needle) {
            if ($this->contains($needle, $ignore_case)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a string contains all array values.
     *
     * @param iterable<string>|iterable<static> $needles
     * @param bool $ignore_case
     * @return bool
     */
    public function containsAll(iterable $needles, bool $ignore_case = false): bool
    {
        foreach ($needles as $needle) {
            if (false === $this->contains($needle, $ignore_case)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     * @return static
     */
    abstract public function match(string $pattern): static;

    /**
     * Determine if a string matches a given pattern.
     *
     * @param string $pattern
     * @return bool
     */
    abstract public function matches(string $pattern): bool;

    /**
     * Replace first occurrence of searched values with given replacement.
     *
     * @param string|iterable|static $search
     * @param string|iterable|static $replace
     * @return $this
     */
    public function replace(string|iterable|AbstractString $search, string|iterable|AbstractString $replace): static
    {
        if (false === is_iterable($search)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        $value = $this;
        foreach ($search as $index => $needle) {
            $replacement = is_iterable($replace) ? $replace[$index] : $replace;
            $value = $value->replace($needle, $replacement);
        }

        return $value;
    }

    /**
     * Replace all occurrences of searched values with given replacements.
     *
     * @param string|iterable|static $search
     * @param string|iterable|static $replace
     * @param bool $ignore_case
     * @return static
     */
    abstract public function replaceAll(string|iterable|AbstractString $search, string|iterable|AbstractString $replace, bool $ignore_case = false): static;

    /**
     * Replace the patterns matching given regular expression.
     *
     * @param string $pattern
     * @param string|Closure|static $replace
     * @param int $limit
     * @return static
     */
    abstract public function replaceMatches(string $pattern, string|Closure|AbstractString $replace, int $limit = -1): static;

    /**
     * Remove any occurrence of the given string in the subject.
     *
     * @param string|iterable|static $search
     * @param bool $ignore_case
     * @return static
     */
    public function remove(string|iterable|AbstractString $search, bool $ignore_case = false): static
    {
        return $this->replaceAll($search, '', $ignore_case);
    }

    /**
     * Remove substring matching given regular expression.
     *
     * @param string $pattern
     * @return $this
     */
    public function removeMatches(string $pattern): static
    {
        return $this->replaceMatches($pattern, '');
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param string|static $prefix
     * @return static
     */
    abstract public function start(string|AbstractString $prefix): static;

    /**
     * Convert the string to upper-case.
     *
     * @return static
     */
    abstract public function upper(): static;

    /**
     * Convert the string to title case.
     *
     * @return static
     */
    abstract public function title(): static;

    /**
     * Convert the string to lower-case.
     *
     * @return static
     */
    abstract public function lower(): static;

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string|static $suffix
     * @return static
     */
    abstract public function finish(string|AbstractString $suffix): static;

    /**
     * Wrap the string with the given strings.
     *
     * @param static|string $before
     * @param static|string|null $after
     * @return static
     */
    abstract public function wrap(string|AbstractString $before, string|AbstractString|null $after = null): static;

    /**
     * Determine if a string starts with any given substring.
     *
     * @param string|iterable|static $needles
     * @param bool $ignore_case
     * @return bool
     */
    public function startsWith(string|iterable|AbstractString $needles, bool $ignore_case = false): bool
    {
        if (false === is_iterable($needles)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        foreach ($needles as $needle) {
            if ($this->startsWith($needle, $ignore_case)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a string starts with a number.
     *
     * @return bool
     */
    public function startsWithNumber(): bool
    {
        return $this->matches('/^\d/');
    }

    /**
     * Determine if a string ends with any given substring.
     *
     * @param string|iterable|static $needles
     * @param bool $ignore_case
     * @return bool
     */
    public function endsWith(string|iterable|AbstractString $needles, bool $ignore_case = false): bool
    {
        if (false === is_iterable($needles)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        foreach ($needles as $needle) {
            if ($this->endsWith($needle, $ignore_case)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a string ends with a number.
     *
     * @return bool
     */
    public function endsWithNumber(): bool
    {
        return $this->matches('/\d$/');
    }

    public function toString(): string
    {
        return (string)$this;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}