<?php

declare(strict_types=1);

namespace Filczek\Value\Number\Calculator;

final class Calculator
{
    private static $instance;

    public static function instance(): CalculatorInterface
    {
        if (null === static::$instance) {
            static::$instance = new BcMathCalculator();
        }

        return static::$instance;
    }
}