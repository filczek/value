<?php

declare(strict_types=1);

namespace Filczek\Value\Test\Array;

use Filczek\Value\Array\ListArrayValue;
use Filczek\Value\String\UnicodeString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

class ListArrayValueTest extends TestCase
{
    #[DataProvider('transformsToListArrayProvider')]
    public function testTransformsToListArray(mixed $value, mixed $expected)
    {
        // Arrange

        // Act
        $array = ListArrayValue::of($value);

        // Assert
        $this->assertEquals($expected, $array->toArray());
    }

    public function transformsToListArrayProvider()
    {
        yield "transforms itself to list array" => [ListArrayValue::empty(), []];
        yield "transforms Stringable to list array" => [UnicodeString::empty(), ['']];
        yield "transforms string to list array" => ['string', [0 => 'string']];
        yield "transforms int to list array" => [15, [0 => 15]];
        yield "transforms float to list array" => [15.3, [0 => 15.3]];
        yield "transforms null to empty array" => [null, []];
    }

    #[DataProvider('throwsIfInvalidValueProvider')]
    public function testThrowsIfInvalidValue(mixed $value)
    {
        $this->expectException(Throwable::class);

        ListArrayValue::of($value);
    }

    public function throwsIfInvalidValueProvider()
    {
        yield "throws when associative array" => [['key' => 'value']];
    }
}
