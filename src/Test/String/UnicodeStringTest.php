<?php

declare(strict_types=1);

namespace Filczek\Value\Test\String;

use Filczek\Value\String\AbstractString;
use Filczek\Value\String\UnicodeString;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UnicodeStringTest extends TestCase
{
    #[DataProvider('transformsToStringProvider')]
    public function testTransformsToString(mixed $value, string $expected): void
    {
        // Arrange

        // Act
        $actual = UnicodeString::of($value);

        // Assert
        $this->assertEquals($expected, $actual);
        $this->assertSame($expected, $actual->toString());
    }

    public static function transformsToStringProvider(): Generator
    {
        yield "transforms itself to string" => [UnicodeString::of('test'), 'test'];
        yield "transforms int to string" => [6512, "6512"];
        yield "transforms float to string" => [984.65, "984.65"];
        yield "transforms null to empty string" => [null, ''];
    }

    public function testIsImmutable(): void
    {
        // Arrange
        $a = UnicodeString::of("world");

        // Act
        $b = $a->start("hello ")->finish("!");

        // Assert
        $this->assertEquals("world", $a);
        $this->assertEquals("hello world!", $b);
    }

    #[DataProvider('notEqualsProvider')]
    public function testNotEquals(string|AbstractString $string, string|iterable|AbstractString $other, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->notEquals($other);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function notEqualsProvider(): Generator
    {
        yield 'equals to native string' => ['abc', 'abc', false];
        yield 'equals to any string in array' => ['abc', ['abc'], false];
        yield 'equals to UnicodeString' => ['abc', UnicodeString::of('abc'), false];

        yield 'not equals to native string' => ['abc', 'def', true];
        yield 'not equals to any string in array' => ['abc', ['def'], true];
        yield 'not equals to UnicodeString' => ['abc', UnicodeString::of('def'), true];
    }

    #[DataProvider('squishProvider')]
    public function testSquish(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->squish();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function squishProvider(): Generator
    {
        yield ['', ''];
        yield ['    Zażółć   gęślą         jaźń    ', UnicodeString::of('Zażółć gęślą jaźń')];
    }

    #[DataProvider('trimProvider')]
    public function testTrim(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->trim();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function trimProvider(): Generator
    {
        yield ['', ''];
        yield [' Zażółć gęślą jaźń      ', UnicodeString::of('Zażółć gęślą jaźń')];
    }

    #[DataProvider('trimStartProvider')]
    public function testtrimStart(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->trimStart();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function trimStartProvider(): Generator
    {
        yield ['', ''];
        yield ['      Zażółć gęślą jaźń      ', UnicodeString::of('Zażółć gęślą jaźń      ')];
    }

    #[DataProvider('trimEndProvider')]
    public function testtrimEnd(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->trimEnd();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function trimEndProvider(): Generator
    {
        yield ['', ''];
        yield ['      Zażółć gęślą jaźń      ', UnicodeString::of('      Zażółć gęślą jaźń')];
    }

    #[DataProvider('beforeProvider')]
    public function testBefore(string|AbstractString $string, string|AbstractString $needle, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->before($needle);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function beforeProvider(): Generator
    {
        yield "expects itself it needle is empty" => ['Zażółć gęślą jaźń', '', 'Zażółć gęślą jaźń'];
        yield "expects itself if needle not found" => ['Zażółć gęślą jaźń', 'test', 'Zażółć gęślą jaźń'];
        yield "expects 'Zażółć gęślą ' before first 'jaźń' in 'Zażółć gęślą jaźń jaźń'" => ['Zażółć gęślą jaźń jaźń', 'jaźń', 'Zażółć gęślą '];
    }

    #[DataProvider('beforeLastProvider')]
    public function testBeforeLast(string|AbstractString $string, string|AbstractString $needle, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->beforeLast($needle);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function beforeLastProvider(): Generator
    {
        yield "expects itself if needle is empty" => ['Zażółć gęślą jaźń', '', 'Zażółć gęślą jaźń'];
        yield "expects itself if needle not found" => ['Zażółć gęślą jaźń', 'test', 'Zażółć gęślą jaźń'];
        yield "expects 'Zażółć gęślą jaźń ' before last 'jaźń' in 'Zażółć gęślą jaźń jaźń'" => ['Zażółć gęślą jaźń jaźń', 'jaźń', 'Zażółć gęślą jaźń '];
    }

    #[DataProvider('betweenProvider')]
    public function testBetween(string|AbstractString $string, string|AbstractString $from, string|AbstractString $to, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->between($from, $to);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function betweenProvider(): Generator
    {
        yield "expects itself if any needle is empty" => ['Zażółć gęślą jaźń', '', '', 'Zażółć gęślą jaźń'];
        yield "expects ' gęślą ' between 'Zażółć' and 'jaźń' in 'Zażółć gęślą jaźń" => ['Zażółć gęślą jaźń', 'Zażółć', UnicodeString::of('jaźń'), ' gęślą '];
    }

    #[DataProvider('charAtProvider')]
    public function testCharAt(string|AbstractString $string, int $index, string|AbstractString|false $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->charAt($index);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function charAtProvider(): Generator
    {
        yield "expect false as not found" => ['Zażółć gęślą jaźń', 18, false];
        yield "expect 'ń' at last index" => ['Zażółć gęślą jaźń', -1, 'ń'];
        yield "expect 'ó' at 4th index" => ['Zażółć gęślą jaźń', 3, 'ó'];
        yield "expect 'Z' at first index" => ['Zażółć gęślą jaźń', 0, UnicodeString::of('Z')];
    }

    #[DataProvider('afterProvider')]
    public function testAfter(string|AbstractString $string, string|AbstractString $needle, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->after($needle);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function afterProvider(): Generator
    {
        yield "expects itself it needle is empty" => ['Zażółć gęślą jaźń', '', 'Zażółć gęślą jaźń'];
        yield "expects itself if needle not found" => ['Zażółć gęślą jaźń', 'test', 'Zażółć gęślą jaźń'];
        yield "expects ' gęślą jaźń' after first 'gęślą' in 'Zażółć gęślą gęślą jaźń'" => ['Zażółć gęślą gęślą jaźń', 'gęślą', ' gęślą jaźń'];
    }

    #[DataProvider('afterLastProvider')]
    public function testAfterLast(string|AbstractString $string, string|AbstractString $needle, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->afterLast($needle);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function afterLastProvider(): Generator
    {
        yield "expects itself if needle is empty" => ['Zażółć gęślą jaźń', '', 'Zażółć gęślą jaźń'];
        yield "expects itself if needle not found" => ['Zażółć gęślą jaźń', 'test', 'Zażółć gęślą jaźń'];
        yield "expects ' jaźń' after last 'gęślą' in 'Zażółć gęślą gęślą jaźń'" => ['Zażółć gęślą gęślą jaźń', 'gęślą', ' jaźń'];
    }

    #[DataProvider('indexOfProvider')]
    public function testIndexOf(string|AbstractString $string, string|AbstractString $needle, int|false $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->indexOf($needle);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function indexOfProvider(): Generator
    {
        yield "expects 'x' to be false" => ['Zażółć gęślą jaźń', 'x', false];
        yield "expects 'ż' to be 2nd" => ['Zażółć gęślą jaźń', 'ż', 2];
    }

    #[DataProvider('indexOfLastProvider')]
    public function testIndexOfLast(string|AbstractString $string, string|AbstractString $needle, int|false $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->indexOfLast($needle);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function indexOfLastProvider(): Generator
    {
        yield "expects 'x' to be false" => ['Zażółć gęślą jaźń', 'x', false];
        yield "expects 'ź' to be 15" => ['Zażółć gęślą jaźń', 'ź', 15];
    }

    #[DataProvider('reverseProvider')]
    public function testReverse(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->reverse();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function reverseProvider(): Generator
    {
        yield ['test', 'tset'];
        yield ['jaźń', 'ńźaj'];
    }

    #[DataProvider('lengthProvider')]
    public function testLength(string|AbstractString $string, int $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->length();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function lengthProvider(): Generator
    {
        yield "expects 'AaBb' to be 4 characters" => ['AaBb', 4];
        yield "expects 'Zażółć gęślą jaźń' to be 17 characters" => ['Zażółć gęślą jaźń', 17];
    }

    #[DataProvider('notEmptyProvider')]
    public function testNotEmpty(string|AbstractString $string, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->isNotEmpty();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function notEmptyProvider(): Generator
    {
        yield "expects 'AaBb' to not be empty" => ['AaBb', true];
        yield "expects '' to be empty" => ['', false];
    }

    #[DataProvider('isAsciiProvider')]
    public function testIsAscii(string|AbstractString $string, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->isAscii();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function isAsciiProvider(): Generator
    {
        yield "'' is ASCII (7-bit)" => ['', true];
        yield "'The quick brown fox jumps over the lazy dog' is ASCII (7-bit)" => ['The quick brown fox jumps over the lazy dog', true];
        yield "'Zażółć gęślą jaźń' is not ASCII (7-bit)" => ['Zażółć gęślą jaźń', false];
        yield "Extended ASCII (8 bit) is not ASCII (7-bit)" => ['«Jeder, der sich die Fähigkeit erhält, Schönes zu erkennen, wird nie alt werden.» © Franz Kafka', false];
        yield "❌ is not ASCII (7-bit)" => ['❌', false];
    }

    #[DataProvider('toAsciiProvider')]
    public function testToAscii(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->toAscii();

        // Assert
        $this->assertTrue($actual->isAscii());
        $this->assertEquals($expected, $actual);
    }

    public static function toAsciiProvider(): Generator
    {
        yield "'The quick brown fox jumps over the lazy dog' is the same" => ['The quick brown fox jumps over the lazy dog', 'The quick brown fox jumps over the lazy dog'];
        yield "'Zażółć gęślą jaźń' to 'Zazolc gesla jazn'" => ['Zażółć gęślą jaźń', 'Zazolc gesla jazn'];
        yield "'ZAŻÓŁĆ GĘŚLĄ JAŹŃ' to 'ZAZOLC GESLA JAZN'" => ['ZAŻÓŁĆ GĘŚLĄ JAŹŃ', UnicodeString::of('ZAZOLC GESLA JAZN')];
    }

    #[DataProvider('isJsonProvider')]
    public function testIsJson(string|AbstractString $string, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->isJson();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function isJsonProvider(): Generator
    {
        yield ['', false];
        yield ['[]', true];
        yield ['{}', true];
        yield ['{"hello":"world"}', true];
        yield ['test', false];
    }

    #[DataProvider('containsProvider')]
    public function testContains(string|AbstractString $string, string|iterable|AbstractString $needles, bool $ignore_case, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->contains($needles, $ignore_case);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function containsProvider(): Generator
    {
        yield "empty string is false" => ['Zażółć gęślą jaźń', [''], false, false];

        yield "contains 'Zażółć' in 'Zażółć gęślą jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', 'Zażółć', false, true];
        yield "contains 'zażółć' in 'Zażółć gęślą jaźń' (case insensitive)" => ['Zażółć gęślą jaźń', 'Zażółć', true, true];

        yield "contains 'Zażółć' or 'zażółć' in 'Zażółć gęślą jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', [UnicodeString::of('Zażółć'), 'zażółć'], false, true];
        yield "contains 'zażółć' or 'zażółć' in 'Zażółć gęślą jaźń' (case insensitive)" => ['Zażółć gęślą jaźń', [UnicodeString::of('zażółć'), 'zażółć'], true, true];

        yield "not contains 'jazn' in 'Zażółć gęślą jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', 'jazn', false, false];
        yield "not contains 'jazn' in 'Zażółć gęślą jaźń' (case insensitive)" => ['Zażółć gęślą jaźń', 'jazn', true, false];
    }

    #[DataProvider('containsAllProvider')]
    public function testContainsAll(string|AbstractString $string, iterable $needles, bool $ignore_case, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->containsAll($needles, $ignore_case);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function containsAllProvider(): Generator
    {
        yield "contains 'Zażółć' in 'Zażółć gęślą jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', ['Zażółć'], false, true];

        yield "contains 'Zażółć', 'gęślą' and 'jaźń' in 'Zażółć gęślą jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', ['Zażółć', 'gęślą', 'jaźń'], false, true];
        yield "contains 'zażółć', 'gęślą' and 'jaźń' in 'Zażółć gęślą jaźń' (case insensitive)" => ['Zażółć gęślą jaźń', ['zażółć', 'gęślą', 'jaźń'], true, true];

        yield "not contains 'Zazolc', 'gęślą' and 'jaźń' in 'Zażółć gęślą jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', ['Zazolc', 'gęślą', 'jaźń'], false, false];
        yield "not contains 'zazolc', 'gęślą' and 'jaźń' in 'Zażółć gęślą jaźń' (case insensitive)" => ['Zażółć gęślą jaźń', ['Zazolc', 'gęślą', 'jaźń'], true, false];
    }

    #[DataProvider('matchProvider')]
    public function testMatch(string|AbstractString $string, string $pattern, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->match($pattern);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function matchProvider(): Generator
    {
        yield "expects to return empty string if match was not found" => ['test', '/\d+/', ''];
        yield "expects to return first match of any number in '21test 6854 test'" => ['21test 6854 test', '/\d+/', '21'];
    }

    #[DataProvider('replaceProvider')]
    public function testReplace(string|AbstractString $string, string|iterable|AbstractString $search, string|iterable|AbstractString $replace, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->replace($search, $replace);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function replaceProvider(): Generator
    {
        yield "reutrns itself nothing was replaced" => ['21test', 'hello', 'world', '21test'];
        yield "replaces first occurrence of 'test' with ' years'" => ['21test test', 'test', ' years', '21 years test'];
        yield "replaces '%0' with 'Hello' and '%1' with 'world!'" => ['%0 %1', ['%0', '%1'], ['Hello', 'world!'], 'Hello world!'];
    }

    #[DataProvider('replaceAllProvider')]
    public function testReplaceAll(string|AbstractString $string, string|iterable|AbstractString $search, string|iterable|AbstractString $replace, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->replaceAll($search, $replace);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function replaceAllProvider(): Generator
    {
        yield "replaces every occurrence of 'test' with ' years'" => ['21test test', 'test', ' years', '21 years  years'];
        yield "replaces '%0' with 'Hello' and '%1' with 'world!'" => ['%0 %1', ['%0', '%1'], ['Hello', 'world!'], 'Hello world!'];
    }

    #[DataProvider('removeProvider')]
    public function testRemove(string|AbstractString $string, string|iterable|AbstractString $needles, bool $ignore_case, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->remove($needles, $ignore_case);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function removeProvider(): Generator
    {
        yield ['The quick brown fox jumps over the lazy dog', ['fox', 'over'], false, 'The quick brown  jumps  the lazy dog'];
        yield ['Zażółć gęślą jaźń', 'gęślą', false, 'Zażółć  jaźń'];
        yield ['Zażółć gęślą jaźń', 'zażółć', false, 'Zażółć gęślą jaźń'];
        yield ['Zażółć gęślą jaźń', 'zażółć', true, ' gęślą jaźń'];
    }

    #[DataProvider('removeMatchesProvider')]
    public function testRemoveMatches(string|AbstractString $string, string $pattern, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->removeMatches($pattern);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function removeMatchesProvider(): Generator
    {
        yield "removes every number in a string" => ['21test21', '/\d+/', 'test'];
    }

    #[DataProvider('upperProvider')]
    public function testUpper(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->upper();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function upperProvider(): Generator
    {
        yield ['The quick brown fox jumps over the lazy dog', 'THE QUICK BROWN FOX JUMPS OVER THE LAZY DOG'];
        yield ['Zażółć gęślą jaźń', 'ZAŻÓŁĆ GĘŚLĄ JAŹŃ'];
    }

    #[DataProvider('titleProvider')]
    public function testTitle(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->title();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function titleProvider(): Generator
    {
        yield ['the', 'The'];
        yield ['żażółć', 'Żażółć'];
    }

    #[DataProvider('lowerProvider')]
    public function testLower(string|AbstractString $string, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->lower();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function lowerProvider(): Generator
    {
        yield ['THE QUICK BROWN FOX JUMPS OVER THE LAZY DOG', 'the quick brown fox jumps over the lazy dog'];
        yield ['ZAŻÓŁĆ GĘŚLĄ JAŹŃ', 'zażółć gęślą jaźń'];
    }

    #[DataProvider('wrapProvider')]
    public function testWrap(string|AbstractString $string, string|iterable|AbstractString $before, string|iterable|AbstractString|null $after, string|AbstractString $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->wrap($before, $after);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public static function wrapProvider(): Generator
    {
        yield "wraps '/w' to '/\/w/i'" => ['/w', '/\\', '/i', '/\/w/i'];
        yield "wraps 'Hello world!' in quotes" => ['Hello world!', '"', null, '"Hello world!"'];
    }

    #[DataProvider('startsWithProvider')]
    public function testStartsWith(string|AbstractString $string, string|iterable|AbstractString $needles, bool $ignore_case, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->startsWith($needles, $ignore_case);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function startsWithProvider(): Generator
    {
        yield 'expects empty needle to be false' => ['year 21', '', false, false];

        yield "'21 years' starts with '21' (case sensitive)" => ['21 years', '21', false, true];
        yield "'g_Width' starts with 's' or 'g' (case sensitive)" => ['g_Width', ['s', 'g'], false, true];
        yield "'g_Width' not starts with 's' (case sensitive)" => ['g_Width', ['s'], false, false];

        yield "'Zażółć gęślą jaźń' starts with 'Zażółć' (case sensitive)" => ['Zażółć gęślą jaźń', 'Zażółć', false, true];
        yield "'Zażółć gęślą jaźń' starts with 'zażółć' (case insensitive)" => ['Zażółć gęślą jaźń', 'Zażółć', true, true];
    }

    #[DataProvider('startsWithNumberProvider')]
    public function testStartsWithNumber(string|AbstractString $string, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->startsWithNumber();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function startsWithNumberProvider(): Generator
    {
        yield ['21 years', true];
        yield [' 21 years', false];
        yield ['years 21', false];
        yield ['years 21 ', false];
    }

    #[DataProvider('endsWithProvider')]
    public function testEndsWIth(string|AbstractString $string, string|iterable|AbstractString $needles, bool $ignore_case, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->endsWith($needles, $ignore_case);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function endsWithProvider(): Generator
    {
        yield 'expects empty needle to be false' => ['year 21', '', false, false];

        yield "'year 21' ends with '21' (case sensitive)" => ['year 21', '21', false, true];
        yield "'g_Width' ends with 'Width' or 'Height' (case sensitive)" => ['g_Width', ['Width', 'Height'], false, true];
        yield "'g_Width' not ends with 'Height' (case sensitive)" => ['g_Width', ['Height'], false, false];

        yield "'Zażółć gęślą jaźń' ends with 'jaźń' (case sensitive)" => ['Zażółć gęślą jaźń', 'jaźń', false, true];
        yield "'Zażółć gęślą jaźń' ends with 'jaźń' (case insensitive)" => ['Zażółć gęślą jaźń', 'jaźń', true, true];
    }

    #[DataProvider('endsWithNumberProvider')]
    public function testEndsWithNumber(string|AbstractString $string, bool $expected): void
    {
        // Arrange
        $string = UnicodeString::of($string);

        // Act
        $actual = $string->endsWithNumber();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public static function endsWithNumberProvider(): Generator
    {
        yield ['21 years', false];
        yield [' 21 years', false];
        yield ['years 21', true];
        yield ['years 21 ', false];
    }
}
