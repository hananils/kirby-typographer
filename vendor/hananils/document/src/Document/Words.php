<?php

namespace Hananils\Document;

use ArrayIterator;
use FilterIterator;

/**
 * Words class representing a collection of words that can be filtered
 * and iterated.
 */
class Words extends FilterIterator
{
    const WORDS = 1;
    const SEPARATORS = 2;
    const ALL = 3;

    protected int $position = 0;

    public function __construct(
        string $text,
        protected int $output = self::WORDS
    ) {
        $words = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $iterator = new ArrayIterator($words);
        parent::__construct($iterator);
    }

    /**
     * Accepts or rejects words while filtering. Either returns word and
     * separators, words only or separators only.
     */
    public function accept(): bool
    {
        $this->position++;

        if ($this->output === self::WORDS) {
            if ($this->position % 2 !== 0) {
                return true;
            }
        } elseif ($this->output === self::SEPARATORS) {
            if ($this->position % 2 === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set output type.
     */
    public function setOutput($output = self::ALL): self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get output type.
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets word filter.
     */
    public function words(): self
    {
        $this->setOutput(self::WORDS);

        return $this;
    }

    /**
     * Sets separator filter.
     */
    public function separators(): self
    {
        $this->setOutput(self::SEPARATORS);

        return $this;
    }

    /**
     * Removes filters.
     */
    public function all(): self
    {
        $this->setOutput(self::ALL);

        return $this;
    }

    /**
     * Converts the filterd collection to string.
     */
    public function toString(): string
    {
        $string = '';

        // Using a loop will make sure we only process the filtered set,
        // either words, separators or all.
        foreach ($this as $item) {
            $string .= $item;
        }

        return $string;
    }
}
