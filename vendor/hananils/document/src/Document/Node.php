<?php

namespace Hananils\Document;

use Dom\HtmlDocument;
use Dom\Element;
use Dom\Text;
use Hananils\Document\Characters;
use Hananils\Document\Node\CanMutate;
use Hananils\Document\Node\CanTraverse;
use Hananils\Document\Node\CanTrim;
use Hananils\Document\Node\HasAttributes;
use Hananils\Document\Node\HasState;

/**
 * Node class representing a single `DomNode`. It provides an advanced interface
 * to read, mutate or traverse the node an it's content.
 */
class Node implements \Stringable
{
    use CanMutate;
    use CanSelect;
    use CanTraverse;
    use CanTrim;
    use HasAttributes;
    use HasState;

    public HtmlDocument|null $document = null;
    protected HtmlDocument|Element|Text|null $item = null;
    protected int|null $index = null;
    protected Nodes|null $iterator = null;
    protected string $name = 'text';

    public function __construct(
        HtmlDocument|Element|Text $item,
        int|null $index = null,
        Nodes|null $iterator = null
    ) {
        $this->item = $item;
        $this->index = $index;
        $this->iterator = $iterator;

        if ($item) {
            $this->document = $item->ownerDocument;
        }

        $this->name = $this->item->localName ?? 'text';
    }

    /**
     * Returns the `DomNode`.
     */
    public function item(): HtmlDocument|Element|Text|null
    {
        return $this->item;
    }

    /**
     * Returns the parent `DomDocument`.
     */
    public function document(): HtmlDocument|null
    {
        return $this->document;
    }

    /**
     * If defined, returns the index of the current node in the parent iterator.
     */
    public function index(): int|null
    {
        return $this->index;
    }

    /**
     * If defined, return the iterator the current node is part of.
     */
    public function iterator(): Nodes|null
    {
        return $this->iterator;
    }

    /**
     * Returns the element name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the element categories of the current node. This could be
     * something like `flow`, `phrasing` or any other category defined in the
     * HTML spec. Additionally, the categories can also contain the value
     * `preformatted` for elements that should not be altered by formatters,
     * e. g. content within a `pre` or `style` element.
     *
     * See the HTML spec for more details:
     * https://html.spec.whatwg.org/multipage/indices.html#row-content-categories.
     */
    public function categories(): array|null
    {
        if ($this->isText()) {
            $name = $this->item->parentElement->localName;
        } else {
            $name = $this->name;
        }

        return Elements::getCategories($name);
    }

    public function categoriesIncludingAncestors(): array|null
    {
        $all = [];

        foreach ($this->parentsAndSelf() as $node) {
            if ($categories = $node->categories()) {
                $all = array_merge($all, $categories);
            }
        }

        return array_unique($all);
    }

    /**
     * Returns the position of the node in relation to its siblings. Unlike the
     * index, position in one-based and the first node will return a position
     * of `1`.
     */
    public function position(): int
    {
        return $this->prevAll()->count() + 1;
    }

    /**
     * Returns the position of the node in relation to its siblings of the same
     * type, say all paragraphs or all lists. Unlike the index, position in
     * one-based and the first node will return a position of `1`.
     */
    public function positionOfType(): int
    {
        $preceding = $this->prevAll()->filterBy('name', '==', $this->name());

        return $preceding->count() + 1;
    }

    /**
     * Converts the current node to HTML.
     */
    public function toHtml(): string
    {
        return $this->document->saveHtml($this->item);
    }

    /**
     * Takes the current node's content and returns it as a character collection.
     */
    public function toCharacters(): Characters
    {
        $text = $this->item->textContent;

        return new Characters($text);
    }

    /**
     * Takes the current node's content and returns it as a word collection.
     */
    public function toWords(): Words
    {
        $text = $this->item->textContent;

        return new Words($text);
    }

    /**
     * Converts the current node to a string representation by converting it
     * to HTML.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
