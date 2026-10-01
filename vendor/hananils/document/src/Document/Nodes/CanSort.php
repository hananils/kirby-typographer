<?php

namespace Hananils\Document\Nodes;

trait CanSort
{
    /**
     * Reversed the nodes iterator.
     */
    public function reverse(): self
    {
        $nodes = $this->getInnerIterator();

        $nodes->uksort(function (mixed $a, mixed $b) {
            if ($a == $b) {
                return 1;
            } else {
                return $b - $a;
            }
        });

        $this->rewind();

        return $this;
    }
}
