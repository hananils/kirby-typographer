<?php

namespace Hananils\Document\Nodes;

use Hananils\Document\Node;

trait CanCount
{
    /**
     * Counts the items.
     */
    public function count(): int
    {
        $nodes = $this->toArray();

        return count($nodes);
    }

    /**
     * Checks if the filtered iterator is empty.
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Checks if the filtered iterator is not empty.
     */
    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }
}
