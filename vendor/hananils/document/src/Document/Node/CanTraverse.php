<?php

namespace Hananils\Document\Node;

use Hananils\Document\Node;
use Hananils\Document\Nodes;

trait CanTraverse
{
    /**
     * Returns preceding sibling node.
     */
    public function prev(): null|Node
    {
        if ($this->item->previousSibling === null) {
            return null;
        }

        return new Node($this->item->previousSibling);
    }

    /**
     * Returns all preceding sibling nodes.
     */
    public function prevAll(): Nodes
    {
        $siblings = new Nodes();
        $current = $this->item;

        while ($current->previousSibling !== null) {
            $current = $current->previousSibling;
            $siblings->append($current);
        }

        return $siblings;
    }

    /**
     * Returns following sibling node.
     */
    public function next(): null|Node
    {
        if ($this->item->nextSibling === null) {
            return null;
        }

        return new Node($this->item->nextSibling);
    }

    /**
     * Returns all following sibling nodes.
     */
    public function nextAll(): Nodes
    {
        $siblings = new Nodes();
        $current = $this->item;

        while ($current->nextSibling !== null) {
            $current = $current->nextSibling;
            $siblings->append($current);
        }

        return $siblings;
    }

    /**
     * Returns the nodes parent.
     *
     * @param $contained If true, only returns parents within the document body.
     */
    public function parent($contained = true): Node|null
    {
        if (!$this->item) {
            return null;
        }

        $parent = $this->item->parentNode;

        if (
            !$parent ||
            ($contained === true &&
                in_array($parent->localName, ['body', 'html']))
        ) {
            return null;
        }

        return new Node($parent);
    }

    /**
     * Returns all node parents.
     */
    public function parents(): Nodes
    {
        return $this->ancestors(includeSelf: false);
    }

    /**
     * Returns all node parents and the node itself.
     */
    public function parentsAndSelf(): Nodes
    {
        return $this->ancestors(includeSelf: true);
    }

    /**
     * Returns the node ancestors.
     *
     * @param $includeSelf Flag to include the current node.
     */
    private function ancestors($includeSelf = false): Nodes
    {
        $nodes = new Nodes();
        $current = $this->item;

        if ($includeSelf === true) {
            $nodes->append($current);
        }

        while (
            $current->parentNode &&
            !in_array($this->name, ['body', 'html'])
        ) {
            $current = $current->parentNode;
            $nodes->append($current);
        }

        return $nodes;
    }

    /**
     * Returns all child nodes. To get elements only, use `children`.
     */
    public function childNodes(): Nodes
    {
        $nodes = new Nodes();
        $nodes->appendAll($this->item->childNodes);

        return $nodes;
    }

    /**
     * Returns the element inner HTML.
     */
    public function content()
    {
        return $this->childNodes()->toHtml();
    }

    /**
     * Returns all child element. To get all child node only, use `childNodes`.
     */
    public function children(): Nodes
    {
        $nodes = new Nodes();

        foreach ($this->item->childNodes as $child) {
            if ($child->nodeType === 1) {
                $nodes->append($child);
            }
        }

        return $nodes;
    }

    /**
     * Travers all child nodes and apply a method.
     *
     * @param $method The method that is applied to all traversed nodes.
     * @param $arguments The method arguments.
     */
    private function traverseChildNodes($method, $arguments = []): self
    {
        foreach (
            $this->query('descendant::text()|descendant::*')
            as $childNode
        ) {
            $childNode->{$method}(...$arguments);
        }

        return $this;
    }

    /**
     * Travers all child elements and apply a method.
     *
     * @param $method The method that is applied to all traversed nodes.
     * @param $arguments The method arguments.
     */
    private function traverseChildren($method, $arguments = []): self
    {
        foreach ($this->query('descendant::*') as $child) {
            $child->{$method}(...$arguments);
        }

        return $this;
    }

    /**
     * Travers all text nodes and apply a method.
     *
     * @param $method The method that is applied to all traversed nodes.
     * @param $arguments The method arguments.
     */
    private function traverseText($method, $arguments = []): self
    {
        foreach ($this->query('descendant::text()') as $text) {
            $text->{$method}(...$arguments);
        }

        return $this;
    }
}
