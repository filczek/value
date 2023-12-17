<?php

declare(strict_types=1);

namespace Filczek\Value\Number\Calculator;

interface CalculatorInterface
{
    public function precision(): int;

    public function add(string $a, string $b): string;

    public function subtract(string $a, string $b): string;

    public function multiply(string $a, string $b): string;

    public function divide(string $a, string $b): string;

    /**
     * 0 if the two operands are equal, 1 if the left_operand is larger than the right_operand, -1 otherwise.
     *
     * @param string $a
     * @param string $b
     * @return int
     */
    public function compare(string $a, string $b): int;
}
