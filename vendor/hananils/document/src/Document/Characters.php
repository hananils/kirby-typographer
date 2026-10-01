<?php

namespace Hananils\Document;

use ArrayIterator;

/**
 * Characters class representing a collection of characters that can be iterated.
 */
class Characters extends ArrayIterator
{
    public function __construct(private readonly string $text = '')
    {
        $characters = [];

        foreach (mb_str_split($this->text) as $index => $character) {
            $characters[] = new Character($character, $index, $this);
        }

        parent::__construct($characters);
    }

    /**
     * Returns the input text.
     */
    public function text(): string
    {
        return $this->text;
    }
}
