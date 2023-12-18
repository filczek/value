<?php

declare(strict_types=1);

namespace Filczek\Value\Number;

use Override;

class NumberValue extends AbstractNumber
{
    #[Override]
    public function add(int|float|string|iterable|AbstractNumber $addends): static
    {
        if (is_iterable($addends)) {
            return parent::add($addends);
        }

        $addend = static::of($addends);
        $result = $this->calculator()->add($this->toString(), $addend->toString());

        return static::of($result);
    }

    #[Override]
    public function subtract(int|float|string|iterable|AbstractNumber $subtrahends): static
    {
        if (is_iterable($subtrahends)) {
            return parent::subtract($subtrahends);
        }

        $subtrahend = static::of($subtrahends);
        $result = $this->calculator()->subtract($this->toString(), $subtrahend->toString());

        return static::of($result);
    }

    #[Override]
    public function multiply(int|float|string|iterable|AbstractNumber $multipliers): static
    {
        if (is_iterable($multipliers)) {
            return parent::multiply($multipliers);
        }

        $multiplier = static::of($multipliers);
        $result = $this->calculator()->multiply($this->toString(), $multiplier->toString());

        return static::of($result);
    }

    #[Override]
    public function divide(int|float|string|iterable|AbstractNumber $divisors): static
    {
        if (is_iterable($divisors)) {
            return parent::divide($divisors);
        }

        $divisor = static::of($divisors);
        $result = $this->calculator()->divide($this->toString(), $divisor->toString());

        return static::of($result);
    }

    #[Override]
    public function equals(float|int|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        return 0 === $this->calculator()->compare($this->number, $number->number);
    }

    #[Override]
    public function largerThan(float|int|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result > 0;
    }

    #[Override]
    public function largerThanOrEqual(float|int|string|AbstractNumber $number)
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result >= 0;
    }

    #[Override]
    public function lessThan(float|int|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result < 0;
    }

    #[Override]
    public function lessThanOrEqual(float|int|string|AbstractNumber $number): bool
    {
        $number = static::of($number);

        $result = $this->calculator()->compare($this->toString(), $number->toString());

        return $result <= 0;
    }
}