<?php

declare(strict_types=1);

namespace Filczek\Value\Number;

use Override;

class NumberValue extends AbstractNumber
{
    #[Override]
    public function add(string|int|AbstractNumber|float ...$addends): static
    {
        if (empty($addends)) {
            return static::of($this);
        }

        if (count($addends) > 1) {
            return parent::add(...$addends);
        }

        $addend = static::of($addends[0]);
        $result = $this->calculator()->add($this->toString(), $addend->toString());

        return static::of($result);
    }

    #[Override]
    public function subtract(string|int|AbstractNumber|float ...$subtrahends): static
    {
        if (empty($subtrahends)) {
            return static::of($this);
        }

        if (count($subtrahends) > 1) {
            return parent::subtract(...$subtrahends);
        }

        $subtrahend = static::of($subtrahends[0]);
        $result = $this->calculator()->subtract($this->toString(), $subtrahend->toString());

        return static::of($result);
    }

    #[Override]
    public function multiply(string|int|AbstractNumber|float ...$multipliers): static
    {
        if (empty($multipliers)) {
            return static::of($this);
        }

        if (count($multipliers) > 1) {
            return parent::multiply(...$multipliers);
        }

        $multiplier = static::of($multipliers[0]);
        $result = $this->calculator()->multiply($this->toString(), $multiplier->toString());

        return static::of($result);
    }

    #[Override]
    public function divide(string|int|AbstractNumber|float ...$divisors): static
    {
        if (empty($divisors)) {
            return static::of($this);
        }

        if (count($divisors) > 1) {
            return parent::divide(...$divisors);
        }

        $divisor = static::of($divisors[0]);
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