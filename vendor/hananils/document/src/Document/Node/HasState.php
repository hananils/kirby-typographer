<?php

namespace Hananils\Document\Node;

use Hananils\Document\Elements;
use Hananils\Document\Node;

trait HasState
{
    /**
     * Matches the current node with the given selector. This method allows for
     * context checks, too, using the cascade. If you'd like to match a single
     * element name, use `is`.
     *
     * @param $expression The expression or selector
     * @param $xpath Flag whether the selector uses CSS or XPath syntax, defaults to CSS.
     */
    public function matches($expression, $xpath = false): bool
    {
        if ($this->isText()) {
            return false;
        }

        if ($xpath === true) {
            return $this->query($expression)->isNotEmpty();
        }

        return $this->item->matches($expression);
    }

    public function exists()
    {
        return $this->item !== null;
    }

    /**
     * Checks if the current node name matches the given one.
     *
     * @param $name The element name.
     */
    public function is(string $name): bool
    {
        return $this->exists() && $this->name() === $name;
    }

    /**
     * Checks if the current node is a text node.
     */
    public function isText(): bool
    {
        return $this->exists() && $this->item->nodeType === 3;
    }

    /**
     * Checks if the current node is an element node.
     */
    public function isElement(): bool
    {
        return $this->exists() && $this->item->nodeType === 1;
    }

    /**
     * Checks if the current node is attached to the document.
     */
    public function isAttached()
    {
        if (!$this->exists()) {
            return false;
        }

        $item = $this->item();
        $parent = $item->parentNode;

        while ($parent !== null) {
            $type = $parent->nodeType;

            if (
                $type === XML_DOCUMENT_NODE ||
                $type === XML_HTML_DOCUMENT_NODE
            ) {
                return true;
            }

            $parent = $parent->parentNode;
        }

        return false;
    }

    /**
     * Checks if the current node is detached from the document.
     */
    public function isDetached()
    {
        return !$this->isAttached();
    }

    /**
     * Checks if the current node is whitespace.
     */
    public function isWhitespace()
    {
        if ($this->isDetached() || !$this->isText()) {
            return false;
        }

        return trim($this->text()) === '';
    }

    /**
     * Checks if the current node is a block level element.
     */
    public function isBlock(): bool
    {
        if ($this->isText()) {
            return false;
        }

        return Elements::isBlock($this->item);
    }

    /**
     * Checks if the current node is at the beginning of a block level element.
     */
    public function isBlockStart(): bool
    {
        $parent = $this->parent();

        if (!$this->isSiblingStart() || !$parent) {
            // not at start or not within a block
            return false;
        }

        $start = true;
        $inBlock = false;
        $item = $this;

        while ($start === true && $parent && $inBlock === false) {
            $item = $item->parent();
            $parent = $item->parent();

            // check context
            if ($item && $parent) {
                $start = $item->isSiblingStart();
                $inBlock = $parent->isBlock();
            }
        }

        return $start;
    }

    /**
     * Checks if the current node is at the end of a block level element.
     */
    public function isBlockEnd(): bool
    {
        $parent = $this->parent();

        if (!$this->isSiblingEnd() || !$parent) {
            // not at end or not within a block
            return false;
        }

        // traverse ancestor tree
        $end = true;
        $inBlock = false;
        $item = $this;

        while ($end === true && $parent && $inBlock === false) {
            $item = $item->parent();
            $parent = $item->parent();

            // check context
            if ($item && $parent) {
                $end = $item->isSiblingEnd();
                $inBlock = $parent->isBlock();
            }
        }

        return $end;
    }

    /**
     * Checks if the current element is phrasing content.
     */
    public function isPhrasing(): bool
    {
        if ($this->isText()) {
            return false;
        }

        return Elements::inCategory('phrasing', $this->item);
    }

    /**
     * Checks if the current element is not phrasing content.
     */
    public function isNotPhrasing(): bool
    {
        return !$this->isPhrasing();
    }

    /**
     * Checks if the current element is inline level content.
     */
    public function isInline(): bool
    {
        if ($this->isText()) {
            return false;
        }

        return Elements::isInline($this->item);
    }

    /**
     * Checks if the current node is at the beginning of a inline level element.
     */
    public function isInlineStart(): bool
    {
        $parent = $this->parent();

        if (!$parent) {
            return $this->isSiblingStart();
        }

        return $parent->isInline() && $this->isSiblingStart();
    }

    /**
     * Checks if the current node is at the end of a inline level element.
     */
    public function isInlineEnd(): bool
    {
        $parent = $this->parent();

        if (!$parent) {
            return $this->isSiblingEnd();
        }

        return $parent->isInline() && $this->isSiblingEnd();
    }

    /**
     * Checks if the current node is the first sibiling.
     */
    public function isSiblingStart(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        // As per the docs, previousSibling should return `null` if no sibling
        // exists. This can cause "Unsupported node type" errors so we have to
        // check the position via the nodes parent.
        $first = $this->item->parentNode->childNodes->item(0);

        return $this->item === $first;
    }

    /**
     * Checks if the current node is the first child, alias for `isSiblingStart`.
     */
    public function isFirst(): bool
    {
        return $this->isSiblingStart();
    }

    /**
     * Checks if the current node is the last sibling.
     */
    public function isSiblingEnd(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        // As per the docs, nextSibling should return `null` if no sibling
        // exists. This can cause "Unsupported node type" errors so we have to
        // check the position via the nodes parent.
        $siblings = $this->item->parentNode->childNodes;
        $last = $siblings->item($siblings->count() - 1);

        return $this->item === $last;
    }

    /**
     * Checks if the current node is the last child, alias for `isSiblingEnd`.
     */
    public function isLast(): bool
    {
        return $this->isSiblingEnd();
    }

    /**
     * Checks if the current node is the only child of its parent.
     */
    public function isOnlyChild(): bool
    {
        return !$this->hasSiblings();
    }

    /**
     * Checks if the current node is at the given position.
     *
     * Please note, that the position is one-based.
     *
     * @param $position The position.
     */
    public function isNth($position): bool
    {
        return $this->position() === $position;
    }

    /**
     * Checks if the current node name is any of the given ones.
     *
     * @param $names The element names.
     */
    public function isAnyOf(array $names): bool
    {
        return array_any($names, function ($name) {
            return $this->is($name);
        });
    }

    /**
     * Checks if the current node equals the given node.
     *
     * @param $node The node.
     */
    public function has(Node $node): bool
    {
        if (!$this->hasChildren()) {
            return false;
        }

        $children = $this->children();

        foreach ($children as $child) {
            if ($child->isEqualNode($node)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the current node contains the given text.
     *
     * @param $text The text.
     */
    public function contains(string $text): bool
    {
        return str_contains($this->getText(), $text);
    }

    /**
     * Checks if the current node starts with the given text.
     *
     * @param $text The text.
     */
    public function startsWith(string $text): bool
    {
        return str_starts_with(trim($this->getText()), $text);
    }

    /**
     * Checks if the current node ends with the given text.
     *
     * @param $text The text.
     */
    public function endsWith(string $text): bool
    {
        return str_ends_with(trim($this->getText()), $text);
    }

    /**
     * Checks if the current node has preceding siblings.
     */
    public function hasPrev(): bool
    {
        return !$this->isSiblingStart();
    }

    /**
     * Checks if the current node has following siblings.
     */
    public function hasNext(): bool
    {
        return !$this->isSiblingEnd();
    }

    /**
     * Checks if the current node has siblings.
     */
    public function hasSiblings(): bool
    {
        return $this->hasPrev() || $this->hasNext();
    }

    /**
     * Checks if the current node has child nodes.
     */
    public function hasChildNodes(): bool
    {
        return $this->item->hasChildNodes();
    }

    /**
     * Checks if the current node has elements children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Checks if the current node has a parent.
     */
    public function hasParent(): bool
    {
        return $this->parent() !== null;
    }
}
