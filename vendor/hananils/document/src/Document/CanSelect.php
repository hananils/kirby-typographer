<?php

namespace Hananils\Document;

use Dom\Element;
use Dom\HtmlDocument;
use Dom\Node as DomNode;
use Dom\XPath;

trait CanSelect
{
    public HtmlDocument|null $document = null;
    protected XPath|null $xpath = null;
    protected Nodes|null $selection = null;
    protected $lastExpression = null;

    /**
     * Returns the XPath object for the current document.
     */
    public function xpath(): XPath
    {
        if (
            $this->xpath === null ||
            $this->document !== $this->xpath->document
        ) {
            $this->xpath = new XPath($this->document);
        }

        return $this->xpath;
    }

    /**
     * Queries the document in relation to the current node and return a Nodes
     * collection.
     *
     * @param $expression The XPath expression.
     * @param $node The context node.
     */
    public function query(string $expression, DomNode|null $node = null): Nodes
    {
        $nodes = new Nodes();

        if ($result = $this->xpath()->query($expression, $node)) {
            $nodes->appendAll($result);
        }

        return $nodes;
    }

    public function querySelectorAll(
        string $expression,
        Element|null $node = null
    ): Nodes {
        $nodes = new Nodes();

        if ($node === null) {
            $node = $this->document;
        }

        if ($result = $node->querySelectorAll($expression)) {
            $nodes->appendAll($result);
        }

        return $nodes;
    }

    /**
     * Selects nodes, either by CSS selectors or by XPath expression.
     *
     * @param $expression The expression or selector
     * @param $xpath Flag whether the selector uses CSS or XPath syntax, defaults to CSS.
     */
    public function select(string $expression = '', bool $xpath = false): self
    {
        $this->selection = $xpath
            ? $this->query($expression)
            : $this->querySelectorAll($expression);
        $this->lastExpression = $expression;

        return $this;
    }

    /**
     * Selects the document.
     */
    public function selectDocument(): self
    {
        $this->selection = $this->toDocumentElement();

        return $this;
    }

    /**
     * Selects content nodes.
     */
    public function selectContent(): self
    {
        $this->selection = $this->toContent();

        return $this;
    }

    /**
     * Selects element nodes.
     */
    public function selectElements(): self
    {
        return $this->select('descendant-or-self::*', xpath: true);
    }

    /**
     * Selects text nodes.
     */
    public function selectTexts(): self
    {
        return $this->select('descendant::text()', xpath: true);
    }

    /**
     * Selects all, alias for `clearSelection`.
     */
    public function selectAll(): self
    {
        return $this->clearSelection();
    }

    /**
     * Clears the current selection.
     */
    public function clearSelection(): self
    {
        $this->selection = null;
        $this->lastExpression = null;

        return $this;
    }

    /**
     * Reduces the document to the current selection.
     */
    public function reduce(): Inspector
    {
        return static::fromNodes($this->toSelection());
    }

    /**
     * Filters the document, either by CSS selectors or by XPath expression, and
     * returns a reduced document.
     *
     * @param $expression The expression
     * @param $xpath Flag whether the selector uses CSS or XPath syntax, defaults to CSS.
     */
    public function filter(string $expression = '', bool $xpath = false): self
    {
        $this->select($expression, $xpath);

        return $this->reduce();
    }
}
