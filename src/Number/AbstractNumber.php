<?php

declare(strict_types=1);

namespace Filczek\Value\Number;

use Filczek\Value\Number\Calculator\Calculator;
use Filczek\Value\Number\Calculator\CalculatorInterface;
use Filczek\Value\Shared\Exception\NonIterableMethodNotImplemented;
use Filczek\Value\String\UnicodeString;
use InvalidArgumentException;
use Stringable;

abstract class AbstractNumber implements Stringable
{
    protected $number;

    protected function __construct($number)
    {
        $this->number = $number;
    }

    public static function of($number): static
    {
        if ($number instanceof static) {
            return new static($number->number);
        }

        if (is_int($number)) {
            return static::fromInt($number);
        }

        if (is_float($number)) {
            return static::fromFloat($number);
        }

        if (is_string($number)) {
            return static::fromString($number);
        }

        $type = gettype($number);
        throw new InvalidArgumentException("Unsupported number ($type) type.");
    }

    public static function fromInt(int $number): static
    {
        return new static((string)$number);
    }

    public static function fromFloat(float $number): static
    {
        return new static((string)$number);
    }

    public static function fromString(string $number): static
    {
        $string = UnicodeString::of($number)
            ->squish();

        if ($string->startsWith('+')) {
            $string = $string->replace('+', '');
        }

        if ($string->endsWith('.')) {
            $string = $string->finish('0');
        }

        if (false === $string->matches('/^(\-{0,1})\d+(\.\d{0,})?$/')) {
            throw new InvalidArgumentException("Passed string is not a number!");
        }

        return new static((string)$string);
    }

    public static function sum(int|float|string|AbstractNumber $first = 0, int|float|string|AbstractNumber ...$numbers): static
    {
        return static::of($first)
            ->add($numbers);
    }

    public static function avg(int|float|string|AbstractNumber $first = 0, int|float|string|AbstractNumber ...$numbers): static
    {
        return static::of($first)
            ->add($numbers)
            ->divide(count($numbers) + 1);
    }

    protected function calculator(): CalculatorInterface
    {
        return Calculator::instance();
    }

    /**
     * @param int|float|string|iterable|static $addends
     * @return static
     */
    public function add(int|float|string|iterable|AbstractNumber $addends): static
    {
        if (false === is_iterable($addends)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        $value = static::of($this);
        foreach ($addends as $number) {
            $value = $value->add($number);
        }

        return $value;
    }

    /**
     * @param int|float|string|iterable|static $subtrahends
     * @return static
     */
    public function subtract(int|float|string|iterable|AbstractNumber $subtrahends): static
    {
        if (false === is_iterable($subtrahends)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        $value = $this;
        foreach ($subtrahends as $number) {
            $value = $value->subtract($number);
        }

        return $value;
    }

    /**
     * @param int|float|string|iterable|static $multipliers
     * @return static
     */
    public function multiply(int|float|string|iterable|AbstractNumber $multipliers): static
    {
        if (false === is_iterable($multipliers)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        $value = $this;
        foreach ($multipliers as $number) {
            $value = $value->multiply($number);
        }

        return $value;
    }

    /**
     * @param int|float|string|iterable|static $divisors
     * @return static
     */
    public function divide(int|float|string|iterable|AbstractNumber $divisors): static
    {
        if (false === is_iterable($divisors)) {
            throw NonIterableMethodNotImplemented::of(__FUNCTION__, static::class);
        }

        $value = $this;
        foreach ($divisors as $number) {
            $value = $value->divide($number);
        }

        return $value;
    }

    /**
     * Determine if number is equal to given number.
     *
     * @param int|float|string|static $number
     * @return bool
     */
    public function equals(int|float|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        return 0 === $this->calculator()->compare($this->toString(), $number->toString());
    }

    /**
     * Determine if number is not equal to given number.
     *
     * @param int|float|string|static $number
     * @return bool
     */
    public function notEquals(int|float|string|AbstractNumber $number): bool
    {
        return false === $this->equals($number);
    }

    /**
     * Determine if number is larger than given number.
     *
     * @param int|float|string|static $number
     * @return bool
     */
    public function largerThan(int|float|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result > 0;
    }

    /**
     * Determine if number is larger than or equal to given number.
     *
     * @param int|float|string|static $number
     * @return bool
     */
    public function largerThanOrEqual(int|float|string|AbstractNumber $number)
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result >= 0;
    }

    /**
     * Determine if number is less than given number.
     *
     * @param int|float|string|static $number
     * @return bool
     */
    public function lessThan(int|float|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result < 0;
    }

    /**
     * Determine if number is less than or equal to given number.
     *
     * @param int|float|string|AbstractNumber $number
     * @return bool
     */
    public function lessThanOrEqual(int|float|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result <= 0;
    }

    /**
     * Determine if number is larger than zero.
     *
     * @return bool
     */
    public function isPositive(): bool
    {
        return false === $this->isNegative();
    }

    /**
     * Determine if number is equal to zero.
     *
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->equals(0);
    }

    /**
     * Determine if number is less than zero.
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->number[0] === '-';
    }

    /**
     * Negate the number.
     *
     * @return static
     */
    public function negate(): static
    {
        return $this->multiply(-1);
    }

    /**
     * Transform the number to positive number.
     *
     * @return bool
     */
    public function toPositive(): static
    {
        if ($this->isNegative()) {
            return $this->negate();
        }

        return static::of($this);
    }

    /**
     * Transform the number to negative number.
     *
     * @return static
     */
    public function toNegative(): static
    {
        if ($this->isPositive()) {
            return $this->negate();
        }

        return static::of($this);
    }

    /**
     * Transform the number to integer.
     *
     * @return int
     */
    public function toInt(): int
    {
        return (int)$this->number;
    }

    /** @return string[] */
    protected function split(): array
    {
        $portions = explode('.', $this->number, 2);

        $integer = $portions[0];
        $fractional = rtrim($portions[1] ?? '', '0');

        return [$integer, $fractional];
    }

    /**
     * Determine if number is integer.
     *
     * @return bool
     */
    public function isInt(): bool
    {
        [, $fractional] = $this->split();

        if (empty($fractional)) {
            return true;
        }

        return false;
    }

    /**
     * Transform the number to float.
     *
     * @return float
     */
    public function toFloat(): float
    {
        return (float)$this->number;
    }

    /**
     * Determine if number is a float.
     *
     * @return bool
     */
    public function isFloat(): bool
    {
        return false === $this->isInt();
    }

    /**
     * Transform the number to string.
     *
     * @return string
     */
    public function toString(): string
    {
        [$integer, $fractional] = $this->split();

        if ($this->isInt()) {
            return $integer;
        }

        return "$integer.$fractional";
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}