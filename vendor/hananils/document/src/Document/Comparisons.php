<?php

namespace Hananils\Document;

use Stringable;
use DateTime;

enum Comparisons: string
{
    case Equal = '==';
    case NotEqual = '!=';
    case GreaterThan = '>';
    case GreaterOrEqualThan = '>=';
    case SmallerThan = '<';
    case SmallerOrEqualThan = '<=';
    case Contains = '*=';
    case NotContains = '!*=';
    case StartsWith = '^=';
    case NotStartsWith = '!^=';
    case EndsWith = '$=';
    case NotEndsWith = '!$=';
    case RegEx = '*';
    case NotRegEx = '!*';
    case Between = '..';
    case NotBetween = '!..';
    case AliasBetween = 'between';
    case AliasNotBetween = 'not between';
    case In = 'in';
    case NotIn = 'not in';
    case Includes = 'includes';
    case Excludes = 'excludes';
    case Some = 'some';
    case MaxLength = 'maxlength';
    case MinLength = 'minlength';
    case MaxWords = 'maxwords';
    case MinWords = 'minwords';
    case DateEqual = 'date ==';
    case DateNotEqual = 'date !=';
    case DateGreaterThan = 'date >';
    case DateGreaterOrEqualThan = 'date >=';
    case DateSmallerThan = 'date <';
    case DateSmallerOrEqualThan = 'date <=';
    case DateBetween = 'date ..';
    case DateNotBetween = 'date !..';
    case AliasDateBetween = 'date between';
    case AliasDateNotBetween = 'date not between';

    public function validateEqual(mixed $value, mixed $test): bool
    {
        return $value == $test;
    }

    public function validateNotEqual(mixed $value, mixed $test): bool
    {
        return $value != $test;
    }

    public function validateGreaterThan(mixed $value, mixed $test): bool
    {
        return $value > $test;
    }

    public function validateGreaterOrEqualThan(mixed $value, mixed $test): bool
    {
        return $value >= $test;
    }

    public function validateSmallerThan(mixed $value, mixed $test): bool
    {
        return $value < $test;
    }

    public function validateSmallerOrEqualThan(mixed $value, mixed $test): bool
    {
        return $value <= $test;
    }

    public function validateContains(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return str_contains($value, $test);
    }

    public function validateNotContains(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return !$this->validateContains($value, $test);
    }

    public function validateStartsWith(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return str_starts_with($value, $test);
    }

    public function validateNotStartsWith(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return !$this->validateStartsWith($value, $test);
    }

    public function validateEndsWith(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return str_ends_with($value, $test);
    }

    public function validateNotEndsWith(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return !$this->validateEndsWith($value, $test);
    }

    public function validateRegEx(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return preg_match($test, $value);
    }

    public function validateNotRegEx(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return !$this->validateRegEx($value, $test);
    }

    public function validateBetween(mixed $value, array $test): bool
    {
        return $value > $test[0] && $value < $test[1];
    }

    public function validateNotBetween(mixed $value, array $test): bool
    {
        return !$this->validateBetween($value, $test);
    }

    public function validateAliasBetween(mixed $value, array $test): bool
    {
        return $this->validateBetween($value, $test);
    }

    public function validateAliasNotBetween(mixed $value, array $test): bool
    {
        return !$this->validateBetween($value, $test);
    }

    public function validateMaxLength(Stringable|string $value, int $test): bool
    {
        return strlen($value) <= $test;
    }

    public function validateMinLength(Stringable|string $value, int $test): bool
    {
        return strlen($value) >= $test;
    }

    public function validateMaxWords(Stringable|string $value, int $test): bool
    {
        return str_word_count($value) <= $test;
    }

    public function validateMinWords(Stringable|string $value, int $test): bool
    {
        return str_word_count($value) >= $test;
    }

    public function validateIn(mixed $value, array $test): bool
    {
        return in_array($value, $test);
    }

    public function validateNotIn(mixed $value, array $test): bool
    {
        return !$this->validateIn($value, $test);
    }

    public function validateIncludes(array $value, mixed $test): bool
    {
        return in_array($test, $value);
    }

    public function validateExcludes(array $value, mixed $test): bool
    {
        return count(array_intersect($value, (array) $test)) === 0;
    }

    public function validateSome(array $value, array $test): bool
    {
        return count(array_intersect($value, $test)) <= count($test);
    }

    public function validateDateEqual(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return new DateTime($value) == new DateTime($test);
    }

    public function validateDateNotEqual(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return !$this->validateDateEqual($value, $test);
    }

    public function validateDateGreaterThan(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return new DateTime($value) > new DateTime($test);
    }

    public function validateDateGreaterOrEqualThan(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return new DateTime($value) >= new DateTime($test);
    }

    public function validateDateSmallerThan(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return new DateTime($value) > new DateTime($test);
    }

    public function validateDateSmallerOrEqualThan(
        Stringable|string $value,
        Stringable|string $test
    ): bool {
        return new DateTime($value) >= new DateTime($test);
    }

    public function validateDateBetween(
        Stringable|string $value,
        array $test
    ): bool {
        $value = new DateTime($value);

        return $value > new DateTime($test[0]) &&
            $value < new DateTime($test[1]);
    }

    public function validateDateNotBetween(
        Stringable|string $value,
        array $test
    ): bool {
        return !$this->validateDateBetween($value, $test);
    }

    public function validateAliasDateBetween(
        Stringable|string $value,
        array $test
    ): bool {
        return $this->validateDateBetween($value, $test);
    }

    public function validateAliasDateNotBetween(
        Stringable|string $value,
        array $test
    ): bool {
        return $this->validateDateNotBetween($value, $test);
    }
}
