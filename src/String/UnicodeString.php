<?php

declare(strict_types=1);

namespace Filczek\Value\String;

use Closure;
use Override;
use Transliterator;

class UnicodeString extends AbstractString
{
    #[Override]
    public function equals(string|iterable|AbstractString $string): bool
    {
        if (is_iterable($string)) {
            return parent::equals($string);
        }

        return $this->string === (string)$string;
    }

    #[Override]
    public function trim(): static
    {
        $string = $this->string;
        $result = preg_replace('~^[\s\x{FEFF}\x{200B}]+|[\s\x{FEFF}\x{200B}]+$~u', '', $string) ?? trim($string);

        return static::of($result);
    }

    #[Override]
    public function trimStart(): static
    {
        $string = $this->string;
        $result = preg_replace('~^[\s\x{FEFF}\x{200B}]+~u', '', $string) ?? ltrim($string);

        return static::of($result);
    }

    #[Override]
    public function trimEnd(): static
    {
        $string = $this->string;
        $result = preg_replace('~[\s\x{FEFF}\x{200B}]+$~u', '', $string) ?? rtrim($string);

        return static::of($result);
    }

    #[Override]
    public function charAt(int $index): static|false
    {
        $length = $this->length();

        if ($index < 0 ? $index < -$length : $index > $length - 1) {
            return false;
        }

        return $this->substr($index, 1);
    }

    #[Override]
    public function substr(int $start, ?int $length = null, string $encoding = 'UTF-8'): static
    {
        $result = mb_substr($this->string, $start, $length, $encoding);

        return static::of($result);
    }

    #[Override]
    public function indexOf(string|AbstractString $needle, int $offset = 0, ?string $encoding = null): int|false
    {
        return mb_strpos($this->string, (string)$needle, $offset, $encoding);
    }

    #[Override]
    public function indexOfLast(string|AbstractString $needle, int $offset = 0, ?string $encoding = null): int|false
    {
        return mb_strrpos($this->string, (string)$needle, $offset, $encoding);
    }

    #[Override]
    public function reverse(): static
    {
        $chars = mb_str_split($this->string, 1);
        $result = implode('', array_reverse($chars));

        return static::of($result);
    }

    #[Override]
    public function length(): int
    {
        return mb_strlen($this->string);
    }

    #[Override]
    public function toAscii(): static
    {
        $transliterator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
        $result = $transliterator->transliterate($this->string);

        return static::of($result);
    }

    #[Override]
    public function contains(string|iterable|AbstractString $needles, bool $ignore_case = false): bool
    {
        if (is_iterable($needles)) {
            return parent::contains($needles, $ignore_case);
        }

        $haystack = static::of($this);
        $needle = static::of((string)$needles);

        if ($needle->isEmpty()) {
            return false;
        }

        if ($ignore_case) {
            $haystack = $haystack->lower();
            $needle = $needle->lower();
        }

        if (false !== $haystack->indexOf($needle)) {
            return true;
        }

        return false;
    }

    #[Override]
    public function match(string $pattern): static
    {
        preg_match($pattern, $this->string, $matches);

        if (empty($matches)) {
            return static::empty();
        }

        return static::of($matches[1] ?? $matches[0]);
    }

    #[Override]
    public function matches(string $pattern): bool
    {
        return 1 === preg_match($pattern, $this->string);
    }

    #[Override]
    public function replace(string|iterable|AbstractString $search, string|iterable|AbstractString $replace): static
    {
        if (is_iterable($search)) {
            return parent::replace($search, $replace);
        }

        $search = (string)$search;
        $replace = (string)$replace;

        $index = $this->indexOf($search);
        if (false === $index) {
            return static::of($this);
        }

        $result = substr_replace((string)$this, $replace, $index, mb_strlen($search));

        return static::of($result);
    }

    #[Override]
    public function replaceAll(string|iterable|AbstractString $search, string|iterable|AbstractString $replace, bool $ignore_case = false): static
    {
        $result = $ignore_case
            ? str_ireplace($search, $replace, $this->string)
            : str_replace($search, $replace, $this->string);

        return static::of($result);
    }

    #[Override]
    public function replaceMatches(string $pattern, string|Closure|AbstractString $replace, int $limit = -1): static
    {
        if ($replace instanceof Closure) {
            $result = preg_replace_callback($pattern, $replace, $this->string, $limit);
        } else {
            $result = preg_replace($pattern, (string)$replace, $this->string, $limit);
        }

        return static::of($result);
    }

    #[Override]
    public function start(string|AbstractString $prefix): static
    {
        $quoted = preg_quote((string)$prefix, '/');

        $result = $prefix . preg_replace('/^(?:'.$quoted.')+/u', '', $this->string);

        return static::of($result);
    }

    #[Override]
    public function upper(): static
    {
        $result = mb_strtoupper($this->string, 'UTF-8');

        return static::of($result);
    }

    #[Override]
    public function title(): static
    {
        $result = mb_convert_case($this->string, MB_CASE_TITLE, 'UTF-8');

        return static::of($result);
    }

    #[Override]
    public function lower(): static
    {
        $result = mb_strtolower($this->string, 'UTF-8');

        return static::of($result);
    }

    #[Override]
    public function finish(string|AbstractString $suffix): static
    {
        $quoted = preg_quote((string)$suffix, '/');

        $result = preg_replace('/(?:' . $quoted . ')+$/u', '', $this->string) . $suffix;

        return static::of($result);
    }

    #[Override]
    public function wrap(string|AbstractString $before, string|AbstractString|null $after = null): static
    {
        $before = (string)$before;
        $after = (string)($after ?? $before);

        $result = $before . $this->string . $after;

        return static::of($result);
    }

    #[Override]
    public function startsWith(string|iterable|AbstractString $needles, bool $ignore_case = false): bool
    {
        if (is_iterable($needles)) {
            return parent::startsWith($needles, $ignore_case);
        }

        $haystack = $this->string;
        $needle = (string)$needles;
        if ($ignore_case) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }

        if ('' === $needle) {
            return false;
        }

        if (0 === strncmp($haystack, $needle, mb_strlen($needle))) {
            return true;
        }

        return false;
    }

    #[Override]
    public function endsWith(string|iterable|AbstractString $needles, bool $ignore_case = false): bool
    {
        if (is_iterable($needles)) {
            return parent::endsWith($needles, $ignore_case);
        }

        $haystack = $this->string;
        $needle = (string)$needles;
        if ($ignore_case) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }

        if ('' === $needle) {
            return false;
        }

        if ($needle === mb_substr($haystack, -mb_strlen($needle))) {
            return true;
        }

        return false;
    }
}
