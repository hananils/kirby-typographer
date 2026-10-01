<?php

namespace Hananils\Document\Nodes;

use Hananils\Document\Sequence;

trait CanRestrict
{
    protected int $limit = -1;
    protected int $offset = 0;
    protected Sequence $sequence = Sequence::Every;

    /**
     * Checks if the nodes collection is restricted by either limit or offset
     * settings or even and odd filters.
     */
    public function isRestricted(): bool
    {
        return $this->limit > -1 ||
            $this->offset > 0 ||
            $this->sequence !== Sequence::Every;
    }

    /**
     * Limits the nodes to the given number.
     *
     * @param $count The maximum count.
     */
    public function limit(int $count = -1): self
    {
        $this->limit = $count;

        return $this;
    }

    /**
     * Offsets the nodes by the given number.
     *
     * @param $count The offset count.
     */
    public function offset(int $count = 0): self
    {
        $this->offset = $count;

        return $this;
    }

    /**
     * Restricts the nodes to even items.
     */
    public function even(): self
    {
        $this->sequence = Sequence::Even;

        return $this;
    }

    /**
     * Restricts the nodes to odd items.
     */
    public function odd(): self
    {
        $this->sequence = Sequence::Odd;

        return $this;
    }

    /**
     * Applies the even/odd sequence filters.
     */
    private function applySequence(): void
    {
        if ($this->sequence !== Sequence::Every) {
            if (
                ($this->sequence === Sequence::Even &&
                    $this->position % 2 !== 0) ||
                ($this->sequence === Sequence::Odd && $this->position % 2 === 0)
            ) {
                $this->accepted = false;
            }
        }
    }

    /**
     * Applies the limit filter.
     */
    private function applyLimit(): void
    {
        if (
            $this->limit > 0 &&
            $this->position > $this->offset &&
            $this->position > $this->offset + $this->limit
        ) {
            $this->finished = true;
            $this->accepted = false;
        }
    }

    /**
     * Applies the offset filter.
     */
    private function applyOffset(): void
    {
        if ($this->offset > 0 && $this->position <= $this->offset) {
            $this->accepted = false;
            $this->position++;
        }
    }
}
