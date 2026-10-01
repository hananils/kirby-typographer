<?php

namespace Hananils\Document;

use ArrayIterator;
use IntlChar;
use Stringable;

class Character implements \Stringable
{
    public function __construct(private readonly string $char, private readonly int $index, private readonly Characters $iterator)
    {
    }

    /**
     * Returns the null-based index of the character in relation to its siblings.
     */
    public function index(): int
    {
        return $this->index;
    }

    /**
     * Returns the previous character.
     */
    public function prev(): Character|null
    {
        if (!$this->hasPrev()) {
            return null;
        }

        return $this->iterator->offsetGet($this->index - 1);
    }

    /**
     * Checks if a previous character exists.
     */
    public function hasPrev(): bool
    {
        return $this->iterator->offsetExists($this->index - 1);
    }

    /**
     * Returns the next character.
     */
    public function next(): Character|null
    {
        if (!$this->hasNext()) {
            return null;
        }

        return $this->iterator->offsetGet($this->index + 1);
    }

    /**
     * Returns the previous word.
     */
    public function prevWord(): string
    {
        $prev = $this->prevText();

        return mb_substr($prev, mb_strpos($prev, ' ', -1));
    }

    /**
     * Returns the preceding text.
     */
    public function prevText(): string
    {
        return mb_substr($this->iterator->text(), 0, $this->index + 1);
    }

    /**
     * Returns the next word.
     */
    public function nextWord(): string
    {
        $next = $this->nextText(20);

        return mb_substr($next, 0, mb_strpos($next, ' '));
    }

    /**
     * Returns the following text
     *
     * @param $length The character limit of the returned text.
     */
    public function nextText($length = null): string
    {
        return mb_substr($this->iterator->text(), $this->index + 1, $length);
    }

    /**
     * Checks if a next character exists.
     */
    public function hasNext()
    {
        return $this->iterator->offsetExists($this->index + 1);
    }

    /**
     * Checks if the current character is a first word character.
     */
    public function hasPrevBoundary()
    {
        return !$this->hasPrev() || !$this->prev()->isLetter();
    }

    /**
     * Checks if the current character is a last word character.
     */
    public function hasNextBoundary()
    {
        return !$this->hasNext() || !$this->next()->isLetter();
    }

    /**
     * Compares current character to a given character.
     *
     * @param string|Stringable $comparison The comparison character.
     */
    public function is(string|Stringable $comparison): bool
    {
        return $this->char === (string) $comparison;
    }

    /**
     * Checks if the current character is a letter.
     */
    public function isLetter(): bool
    {
        return IntlChar::isalpha($this->char);
    }

    /**
     * Checks if the current character is a space.
     */
    public function isSpace(): bool
    {
        return IntlChar::isspace($this->char);
    }

    /**
     * Checks if the current character is within a word.
     */
    public function inWord(): bool
    {
        return !$this->isSpace() &&
            $this->hasPrev() &&
            $this->hasNext() &&
            $this->prev()->isLetter() &&
            $this->next()->isLetter();
    }

    /**
     * Returns the current character as string.
     */
    public function __toString(): string
    {
        return $this->char;
    }
}
