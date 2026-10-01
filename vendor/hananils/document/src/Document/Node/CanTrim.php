<?php

namespace Hananils\Document\Node;

use Hananils\Document\Node;

trait CanTrim
{
    /**
     * Trims the whitespace of an element. By default, inner and outer whitespace
     * is taken into account and phrasing as well as preformatted elements will
     * be skipped.
     *
     * @param $before Whether preceding whitespace should be trimmed.
     * @param $after Whether following whitespace should be trimmed.
     * @param $start Whether leading whitespace should be trimmed.
     * @param $end Whether closing whitespace should be trimme.d
     * @param $ignore Elements categories to skip while trimming.
     */
    public function trim(
        bool $before = true,
        bool $after = true,
        bool $start = true,
        bool $end = true,
        array $ignore = []
    ) {
        if ($this->isText()) {
            $trimmed = trim($this->text());
            $this->text($trimmed);

            return;
        }

        // Select current element and all nested child elements
        $trimables = $this->query('descendant-or-self::*', $this->item);

        if (!empty($ignore)) {
            // Ignore elements by category
            $trimables->filterBy(
                'categoriesIncludingAncestors',
                'excludes',
                $ignore
            );
        }

        foreach ($trimables as $trimable) {
            if ($before) {
                $trimable->trimBefore();
            }

            if ($after) {
                $trimable->trimAfter();
            }

            if ($start) {
                $trimable->trimStart();
            }

            if ($end) {
                $trimable->trimEnd();
            }
        }

        return $this;
    }

    /**
     * Trims preceding whitespace.
     */
    public function trimBefore()
    {
        if (
            !$this->item->previousSibling ||
            $this->item->previousSibling->nodeType === XML_DOCUMENT_TYPE_NODE
        ) {
            return;
        }

        $prev = new Node($this->item->previousSibling);

        if ($prev->isWhitespace()) {
            $prev->remove();
        } elseif ($this->isBlock() && $prev->isText()) {
            $trimmed = rtrim($prev->text());

            $prev->text($trimmed);
        }

        return $this;
    }

    /**
     * Trims following whitespace.
     */
    public function trimAfter()
    {
        if (!$this->item->nextSibling) {
            return;
        }

        $next = new Node($this->item->nextSibling);

        if ($next->isWhitespace()) {
            $next->remove();
        } elseif ($this->isBlock() && $next->isText()) {
            $trimmed = ltrim($next->text());

            $next->text($trimmed);
        }

        return $this;
    }

    /**
     * Trims leading whitespace.
     */
    public function trimStart()
    {
        $texts = $this->query('descendant::text()', $this->item);

        foreach ($texts as $text) {
            $item = $text->item();
            $item->textContent = ltrim((string) $item->textContent);

            if (
                $item->parentNode !== null &&
                $text->isWhitespace() &&
                $text->isOnlyChild()
            ) {
                $item->remove();
            } else {
                break;
            }
        }
    }

    /**
     * Trims closing whitespace.
     */
    public function trimEnd()
    {
        $texts = $this->query('descendant::text()', $this->item);

        // We want to trim the ending whitespace so we need to start at the end
        $texts->reverse();

        foreach ($texts as $text) {
            $item = $text->item();
            $item->textContent = rtrim((string) $item->textContent);

            if (
                $item->parentNode !== null &&
                $text->isWhitespace() &&
                $text->isOnlyChild()
            ) {
                $item->remove();
            } else {
                break;
            }
        }
    }
}
