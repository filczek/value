<?php

declare(strict_types=1);

namespace Filczek\Value\Number\Calculator;

final class BcMathCalculator implements CalculatorInterface
{
    public function precision(): int
    {
        return 14;
    }

    public function add(string $a, string $b): string
    {
        return bcadd($a, $b, $this->precision());
    }

    public function subtract(string $a, string $b): string
    {
        return bcsub($a, $b, $this->precision());
    }

    public function multiply(string $a, string $b): string
    {
        return bcmul($a, $b, $this->precision());
    }

    public function divide(string $a, string $b): string
    {
        return bcdiv($a, $b, $this->precision());
    }

    public function compare(string $a, string $b): int
    {
        return bccomp($a, $b, $this->precision());
    }
}