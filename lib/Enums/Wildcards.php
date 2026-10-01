<?php

namespace Hananils\Typographer\Enums;

enum Wildcards: string
{
    case Double = '"';
    case Single = "'";

    public function key(): string
    {
        return strtolower($this->name);
    }

    public function is(string|Wildcards $comparison): bool
    {
        if (is_string($comparison)) {
            return $this->value === $comparison;
        }

        return $this === $comparison;
    }
}
