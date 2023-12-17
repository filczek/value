<?php

declare(strict_types=1);

namespace Filczek\Value\Test\Number;

use Filczek\Value\Number\NumberValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumberValueTest extends TestCase
{
    #[DataProvider('transformsToNumberProvider')]
    public function testTransformsToNumber(mixed $number, string $expected)
    {
        // Arrange

        // Act
        $actual = NumberValue::of($number);

        // Assert
        $this->assertSame((int)$expected, $actual->toInt());
        $this->assertSame((float)$expected, $actual->toFloat());
        $this->assertEquals($expected, $actual);
    }

    public function transformsToNumberProvider()
    {
        yield "transforms int to number value" => [0, "0"];
        yield "transforms float to number value" => [0.15, "0.15"];
        yield "transforms string to number value" => ["1", "1"];
        yield "transforms itself to number value" => [NumberValue::fromInt(1), "1"];
    }

    #[DataProvider('sumProvider')]
    public function testSum(mixed $numbers, string $expected)
    {
        // Arrange

        // Act
        $actual = NumberValue::sum(...$numbers);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function sumProvider()
    {
        yield [[], '0'];
        yield [[1, 2, 3.5, '15'], '21.5'];
    }

    #[DataProvider('avgProvider')]
    public function testAvg(mixed $numbers, string $expected)
    {
        // Arrange

        // Act
        $actual = NumberValue::avg(...$numbers);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function avgProvider()
    {
        yield [[], '0'];
        yield [[1, 2, 3, 4, 5], '3'];
    }

    #[DataProvider('addProvider')]
    public function testAdd(mixed $number, array $addends, string $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->add(...$addends);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function addProvider()
    {
        yield 'empty' => [0, [], '0'];
        yield [0, [1, 2, 3, 4, 5], '15'];
        yield [0, [1, 2.5, 3.5], '7'];
        yield [0, [1, 2.5], '3.5'];
    }

    #[DataProvider('subtrahendProvider')]
    public function testSubtract(mixed $number, array $subtrahends, string $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->subtract(...$subtrahends);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function subtrahendProvider()
    {
        yield 'empty' => [0, [], '0'];
        yield [0, [1, 2, 3, 4, 5], '-15'];
        yield [0, [1, 2.5, 3.5], '-7'];
        yield [0, [1, 2.5], '-3.5'];
    }

    #[DataProvider('multiplierProvider')]
    public function testMultiply(mixed $number, array $multipliers, string $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->multiply(...$multipliers);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function multiplierProvider()
    {
        yield 'empty' => [0, [], '0'];
        yield [0, [1, 2, 3, 4, 5], '0'];
        yield [1, [1, 2, 3, 4, 5], '120'];
        yield [1, [1, 2.5, 3.5], '8.75'];
        yield [1, [1, 2.5], '2.5'];
    }

    #[DataProvider('divideProvider')]
    public function testDivide(mixed $number, array $divisors, string $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->divide(...$divisors);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function divideProvider()
    {
        yield 'empty' => [0, [], '0'];
        yield [0, [1, 2, 3, 4, 5], '0'];
        yield [1, [1, 2, 3, 4, 5], '0.00833333333333'];
        yield [1, [1, 2.5, 3.5], '0.11428571428571'];
        yield [1, [1, 2.5], '0.4'];
    }

    #[DataProvider('largerThanProvider')]
    public function testLargerThan(mixed $number, mixed $b, bool $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->largerThan($b);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function largerThanProvider()
    {
        yield '0 > 0.0' => ['0', '0.0', false];
        yield '0 > 1' => ['0', '1', false];
        yield '1 > 0' => ['1', 0, true];
        yield '0.1 > 1' => ['0.1', NumberValue::of(1), false];
    }

    #[DataProvider('largerThanOrEqualProvider')]
    public function testLargerThanOrEqual(mixed $number, mixed $b, bool $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->largerThanOrEqual($b);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function largerThanOrEqualProvider()
    {
        yield '0 >= 0.0' => ['0', '0.0', true];
        yield '0 >= 1' => ['0', '1', false];
        yield '1 >= 0' => ['1', 0, true];
        yield '0.1 >= 1' => ['0.1', NumberValue::of(1), false];
    }

    #[DataProvider('lessThanProvider')]
    public function testLessThan(mixed $number, mixed $b, bool $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->lessThan($b);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function lessThanProvider()
    {
        yield '0 < 0.0' => ['0', '0.0', false];
        yield '0 < 1' => ['0', '1', true];
        yield '1 < 0' => ['1', 0, false];
        yield '0.1 < 1' => ['0.1', NumberValue::of(1), true];
    }

    #[DataProvider('lessThanOrEqualProvider')]
    public function testLessThanOrEqual(mixed $number, mixed $b, bool $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->lessThanOrEqual($b);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function lessThanOrEqualProvider()
    {
        yield '0 <= 0.0' => ['0', '0.0', true];
        yield '0 <= 1' => ['0', '1', true];
        yield '1 <= 0' => ['1', 0, false];
        yield '0.1 <= 1' => ['0.1', NumberValue::of(1), true];
    }

    #[DataProvider('isPositiveProvider')]
    public function testIsPositive(mixed $number, bool $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->isPositive();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function isPositiveProvider()
    {
        yield ['0', true];
        yield [15, true];
        yield [-25, false];
        yield ['-0', false];
    }

    #[DataProvider('isZeroProvider')]
    public function testIsZero(mixed $number, bool $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->isZero();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function isZeroProvider()
    {
        yield ['0', true];
        yield [15, false];
        yield [-25, false];
        yield ['-0', true];
    }

    #[DataProvider('toPositiveProvider')]
    public function testToPositive(mixed $number, string $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->toPositive();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function toPositiveProvider()
    {
        yield ['0', '0'];
        yield ['-0', '0'];
        yield [15, '15'];
        yield ['-15', '15'];
        yield ['-0.222561', '0.222561'];
    }

    #[DataProvider('toNegativeProvider')]
    public function testToNegative(mixed $number, string $expected)
    {
        // Arrange
        $number = NumberValue::of($number);

        // Act
        $actual = $number->toNegative();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function toNegativeProvider()
    {
        yield ['0', '-0'];
        yield ['-0', '-0'];
        yield [15, '-15'];
        yield ['-15', '-15'];
        yield ['-0.222561', '-0.222561'];
    }
}
