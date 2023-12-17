<?php

declare(strict_types=1);

namespace Filczek\Value\Test\Array;

use Filczek\Value\Array\ArrayValue;
use Filczek\Value\Test\AbstractString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayValueTest extends TestCase
{
    #[DataProvider('atProvider')]
    public function testAt(array|ArrayValue $array, mixed $key, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->at($key);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function atProvider()
    {
        yield "expects 1st index to be 'b'" => [['a', 'b', 'c'], 1, 'b'];
        yield "expects 3rd index to not exist" => [['a', 'b', 'c'], 3, null];
    }

    #[DataProvider('everyProvider')]
    public function testEvery(array|ArrayValue $array, mixed $callback, bool $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->every($callback);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function everyProvider()
    {
        yield 'expects every value to be larger than 0' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v > 0, true];
        yield 'expects every value to not be equal to 0' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v === 0, false];
    }

    #[DataProvider('everyProvider')]
    public function testSome(array|ArrayValue $array, mixed $callback, bool $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->some($callback);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function someProvider()
    {
        yield 'expects some value to be larger than 0' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v > 0, true];
        yield 'expects some value to not be equal to 0' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v === 0, false];
    }

    #[DataProvider('filterProvider')]
    public function testFilter(array|ArrayValue $array, mixed $callback, array $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->filter($callback);

        // Assert
        $this->assertSame($expected, $actual->toArray());
    }

    public static function filterProvider()
    {
        yield 'returns non-empty values' => [[0, null, 1, "", null], null, [2 => 1]];
        yield 'returns every value larger than 3' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v > 3, [3 => 4, 4 => 5, 5 => 6]];
        yield 'returns every value equal to 0' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v === 0, []];
    }

    #[DataProvider('mapProvider')]
    public function testMap(array|ArrayValue $array, \Closure|callable|null $callback, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->map($callback);

        // Assert
        $this->assertEquals($expected, $actual->toArray());
    }

    public static function mapProvider()
    {
        yield 'multiply each number by 2' => [[1, 2, 3], fn (int $v) => $v * 2, [2, 4, 6]];
        yield 'transform each number to string' => [[1, 2, 3], fn (int $v) => (string)$v, ["1", "2", "3"]];
    }

    #[DataProvider('findProvider')]
    public function testFind(array|ArrayValue $array, mixed $callback, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->find($callback);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function findProvider()
    {
        yield 'expects to return null if not found' => [[1, 2, 3], fn (int $v) => $v === 0, null];
        yield 'expects to return 4' => [[1, 2, 3, 4, 5, 6], fn (int $v) => $v > 3, 4];
    }

    #[DataProvider('findLastIndexProvider')]
    public function testFindLastIndex(array|ArrayValue $array, mixed $callback, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->findLastIndex($callback);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function findLastIndexProvider()
    {
        yield 'expects to return false if not found' => [[1, 2, 3, 4, 5, 7, 3, 5], fn (int $v) => $v === 0, false];
        yield 'expects to 7th index' => [[1, 2, 3, 4, 5, 7, 3, 5], fn (int $v) => $v > 3, 7];
    }

    #[DataProvider('findLastProvider')]
    public function testFindLast(array|ArrayValue $array, mixed $callback, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->findLast($callback);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function findLastProvider()
    {
        yield 'expects to return null if not found' => [[1, 2, 3, 4, 5, 7, 3, 5], fn (int $v) => $v === 0, null];
        yield 'expects to return 5' => [[1, 2, 3, 4, 5, 7, 3, 5], fn (int $v) => $v > 3, 5];
    }

    #[DataProvider('flatProvider')]
    public function testFlat(array|ArrayValue $array, array $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->flat();

        // Assert
        $this->assertSame($expected, $actual->toArray());
    }

    public static function flatProvider()
    {
        yield [ [0, 1, 2, [3, 4]], [0, 1, 2, 3, 4] ];
        yield [ [0, 1, [2, [3, [4, 5]]]], [0, 1, 2, 3, 4, 5] ];
    }

    #[DataProvider('containsProvider')]
    public function testContains(array|ArrayValue $array, mixed $value, bool $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->contains($value);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function containsProvider()
    {
        yield "contains 2 in list array" => [[1, 2, 3], 2, true];
        yield "not contains '2' in number list array" => [[1, 2, 3], '2', false];
        yield "contains 'dog' in list array" => [['cat', 'dog', 'bat'], 'dog', true];
        yield "contains 'dog' in associative array" => [['key1' => 'cat', 'key2' => 'dog', 'key3' => 'bat'], 'dog', true];
    }

    #[DataProvider('indexOfProvider')]
    public function testIndexOf(array|ArrayValue $array, mixed $item, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->indexOf($item);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function indexOfProvider()
    {
        yield "index of first 2 is 1" => [[1, 2, 3], 2, 1];
        yield "index of first '2' is false" => [[1, 2, 3], '2', false];
        yield "index of first 'dog' is 1" => [['cat', 'dog', 'bat'], 'dog', 1];
        yield "index of first 'dog' is 'key2'" => [['key1' => 'cat', 'key2' => 'dog', 'key3' => 'bat'], 'dog', 'key2'];
    }

    #[DataProvider('indexOfLastProvider')]
    public function testIndexOfLast(array|ArrayValue $array, mixed $item, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->indexOfLast($item);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function indexOfLastProvider()
    {
        yield "index of last 2 is 3" => [[1, 2, 3, 2], 2, 3];
        yield "index of last '2' is false" => [[1, 2, 3], '2', false];
        yield "index of last 'dog' is 3" => [['cat', 'dog', 'bat', 'dog'], 'dog', 3];
        yield "index of last 'dog' is 'key4'" => [['key1' => 'cat', 'key2' => 'dog', 'key3' => 'bat', 'key4' => 'dog'], 'dog', 'key4'];
    }

    #[DataProvider('joinProvider')]
    public function testJoin(array|ArrayValue $array, string $separator, string|AbstractString $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->join($separator);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function joinProvider()
    {
        yield [[1, 2, 3], ", ", "1, 2, 3"];
        yield [[1, 2, 3], '-', "1-2-3"];
        yield [['cat', 'dog', 'bat'], ", ", "cat, dog, bat"];
        yield [['key1' => 'cat', 'key2' => 'dog', 'key3' => 'bat'], ', ', 'cat, dog, bat'];
    }

    #[DataProvider('reverseProvider')]
    public function testReverse(array|ArrayValue $array, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->reverse();

        // Assert
        $this->assertEquals($expected, $actual->toArray());
    }

    public static function reverseProvider()
    {
        yield [[1, 2, 3], [3, 2, 1]];
        yield [["key1" => 1, "key2" => 2, "key3" => 3], ["key3" => 3, "key2" => 2, "key1" => 1]];
    }

    #[DataProvider('sortProvider')]
    public function testSort(array|ArrayValue $array, callable|null $callback, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->sort($callback);

        // Assert
        $this->assertEquals($expected, $actual->toArray());
    }

    public static function sortProvider()
    {
        yield 'sort' => [[3, 5, 1, 2], null, [1, 2, 3, 5]];
        yield 'sort reverse' => [[3, 5, 1, 2], fn ($a, $b) => $b <=> $a, [5, 3, 2, 1]];
    }

    #[DataProvider('spliceProvider')]
    public function testSplice(array|ArrayValue $array, callable|null $callback, mixed $expected)
    {
        // Arrange
        $array = ArrayValue::of($array);

        // Act
        $actual = $array->sort($callback);

        // Assert
        $this->assertEquals($expected, $actual->toArray());
    }

    public static function spliceProvider()
    {
        yield [[3, 5, 1, 2], null, [1, 2, 3, 5]];
        yield [[3, 5, 1, 2], fn ($a, $b) => $b <=> $a, [5, 3, 2, 1]];
    }
}
