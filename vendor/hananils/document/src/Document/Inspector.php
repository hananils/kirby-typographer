<?php

namespace Hananils\Document;

use Dom\HTMLDocument;
use Hananils\Document\CanSelect;
use IteratorAggregate;
use Stringable;

/**
 * Document Inspector
 *
 * @copyright hana+nils · Büro für Gestaltung
 * @author Nils Hörrmann <nils.hoerrmann@hananils.de>
 * @link https://hananils.de
 * @license All rights reserved
 */
class Inspector implements IteratorAggregate, Stringable
{
    use CanDebug;
    use CanSelect;

    public HtmlDocument|null $document = null;
    public bool $fragment = false;

    public function __construct(string $html = '')
    {
        $html = trim($html);
        $start = strtolower(substr($html, 0, 10));

        if (
            !str_starts_with($start, '<!doctype') &&
            !str_starts_with($start, '<html')
        ) {
            $this->fragment = true;
            $html = "<!DOCTYPE html><html><body>$html</body></html>";
        }

        if ($html === '') {
            $this->document = HTMLDocument::createEmpty();
        } else {
            $this->document = HTMLDocument::createFromString(
                $html,
                LIBXML_NOERROR
            );
        }
    }

    /**
     * Loads HTML from a string.
     *
     * @param $html HTML string to be loaded.
     */
    public static function fromString(string $html)
    {
        return new static($html);
    }

    /**
     * Loads HTML from a file.
     *
     * @param $filename The HTML file to be loaded.
     */
    public static function fromFile(string $filename)
    {
        $html = '';

        if (file_exists($filename)) {
            $html = file_get_contents($filename);
        }

        return new static($html);
    }

    /**
     * Loads HTML from a nodes iterator.
     *
     * @param $nodes The nodes iterator.
     */
    public static function fromNodes(Nodes $nodes)
    {
        $html = $nodes->toHtml();

        return new static($html);
    }

    /**
     * Loads an HTMLDocument.
     *
     * @
     */
    public static function fromDocument(
        HtmlDocument $document,
        bool $fragment = false
    ) {
        $inspector = new static();

        $inspector->document = $document;
        $inspector->fragment = $fragment;

        return $inspector;
    }

    /**
     * Checks if the document is empty.
     */
    public function isEmpty(): bool
    {
        return $this->toRoot()->isEmpty();
    }

    /**
     * Checks if the document is not empty.
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Checks if the document selection applies filters. This is used to carry
     * filters through consecutive method calls which are applied os flags inside
     * the selection unlike the general document `filter` method which reduces
     * the document immediatelly.
     */
    public function isFiltered()
    {
        if (!$this->selection) {
            return false;
        }

        return $this->selection->isRestricted() === true ||
            $this->selection->isFiltered() === true;
    }

    /**
     * Magic method to forwards calls to the current selection.
     *
     * @param $name The method name.
     * @param $arguments The arguments to be passed to the method.
     */
    public function __call(string $name, array $arguments): mixed
    {
        $nodes = $this->toSelection();
        $result = $nodes->{$name}(...$arguments);

        if (in_array($name, ['first', 'nth', 'last']) && $result === false) {
            // Catch emtpy selections that are returned as false
            $this->selection = new Nodes();
        } elseif (is_a($result, 'Hananils\Document\Nodes')) {
            // Pass on Nodes iterator
            $this->selection = $result;
        } elseif (is_a($result, 'Hananils\Document\Node')) {
            // Convert a single Node to a Nodes iterator
            $nodes = new Nodes([$result]);
            $nodes->limit(1);

            $this->selection = $nodes;
        } else {
            // Return all other results directly
            return $result;
        }

        // Reset selection after destructive actions
        if (in_array($name, ['unwrap', 'remove'])) {
            $this->clearSelection();
        }

        // If the resulting selection is filtered, reduce document.
        // Keep in mind that the selection filters are set on the iterator only
        // and have to be propagated to the DOM in order to provide chained
        // methods with the right context.
        if ($this->isFiltered()) {
            return $this->reduce();
        }

        return $this;
    }

    /**
     * Gets the iterator, alias for `toSelection.
     */
    public function getIterator(): Nodes
    {
        return $this->toSelection();
    }

    /**
     * Converts the document to a Nodes iterator, returning either the full
     * document if head and body are set or the content only if the document
     * contains a DOM fragment.
     *
     * If the current selected has been filtered, it will be used and returned instead.
     */
    public function toRoot(): Nodes
    {
        if ($this->isFiltered()) {
            return $this->selection;
        }

        return $this->fragment === true
            ? $this->toContent()
            : $this->toDocumentElement();
    }

    public function toContent()
    {
        $nodes = new Nodes();
        $nodes->appendAll($this->document->body->childNodes);

        return $nodes;
    }

    public function toDocumentElement()
    {
        $nodes = new Nodes();
        $nodes->appendAll([$this->document->documentElement]);

        return $nodes;
    }

    /**
     * Converts the document to a Nodes iterator of the current selection.
     */
    public function toSelection(): Nodes
    {
        return $this->selection ?? $this->toRoot();
    }

    /**
     * Converts the document to a Nodes iterator of the text nodes.
     */
    public function toTexts()
    {
        return $this->selectTexts()->toSelection();
    }

    /**
     * Converts the document to a Nodes iterator of all nodes that are not
     * in a preformatted context.
     */
    public function toFormattables(): Nodes
    {
        return $this->toSelection()->filterBy(
            'categoriesIncludingAncestors',
            'excludes',
            ['preformatted']
        );
    }

    /**
     * Converts the document to a Nodes iterator of all text nodes that are not
     * in a preformatted context.
     */
    public function toFormattableTexts(): Nodes
    {
        return $this->selectTexts()
            ->toSelection()
            ->filterBy('categoriesIncludingAncestors', 'excludes', [
                'preformatted'
            ]);
    }

    /**
     * Converts the document to an HTML string.
     *
     * @param $collapse Collapses the consecutive whitespace if set to true.
     * @param $trim Trims whitespace of block elments if set to true.
     */
    public function toHtml(bool $collapse = false, bool $trim = false): string
    {
        $root = $this->toRoot();

        if ($root->isEmpty()) {
            return '';
        }

        if ($collapse === true) {
            $root->collapse(ignore: ['preformatted']);
        }

        if ($trim === true) {
            $root->trim(ignore: ['phrasing', 'preformatted']);
        }

        $html = $root->toHtml();

        if ($this->fragment === true) {
            return $html;
        }

        return "<!DOCTYPE html>\n" . $html;
    }

    /**
     * Magic method to convert the document to an HTML string without collapsing
     * or trimming whitespace.
     */
    public function __toString(): string
    {
        return $this->toHtml(collapse: false, trim: false);
    }
}
