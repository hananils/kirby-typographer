<?php

namespace Hananils\Document;

use ArrayIterator;
use BadMethodCallException;
use Closure;
use Countable;
use DirectoryIterator;
use Dom\HtmlDocument;
use Dom\NodeList;
use FilterIterator;
use Hananils\Document\Nodes\CanCount;
use Hananils\Document\Nodes\CanFilter;
use Hananils\Document\Nodes\CanRestrict;
use Hananils\Document\Nodes\CanSort;

/**
 * Nodes class representing a collection of nodes that can be counted, filtered
 * and iterated.
 */
class Nodes extends FilterIterator implements Countable, \Stringable
{
    use CanCount;
    use CanFilter;
    use CanRestrict;
    use CanSort;

    protected int $lastIndex = -1;
    protected int $position = 1;
    protected bool $accepted = true;
    protected bool $finished = false;

    public function __construct(array $iterator = [])
    {
        parent::__construct(new ArrayIterator());

        $this->appendAll($iterator);
    }

    /**
     * Appends the given node.
     *
     * @param $value Either a `Node` or a `Dom\Node` object.
     */
    public function append(mixed $value): self
    {
        if (method_exists($value, 'item')) {
            $value = $value->item();
        }

        $nodes = $this->getInnerIterator();
        $this->lastIndex++;

        $nodes[$this->lastIndex] = new Node(
            $value,
            index: $this->lastIndex,
            iterator: $this
        );

        return $this;
    }

    /**
     * Appends all of the given nodes.
     *
     * @param $nodes Either a `Dom\NodeList` or a `Node` array.
     */
    public function appendAll(NodeList|array $nodes): self
    {
        foreach ($nodes as $node) {
            $this->append($node);
        }

        return $this;
    }

    /**
     * Rewinds the iterator and resets filtering.
     */
    public function rewind(): void
    {
        $this->finished = false;
        $this->position = 1;

        parent::rewind();
    }

    /**
     * Returns the first node.
     */
    public function first(): null|Node
    {
        $nodes = $this->toArray();

        return array_first($nodes);
    }

    /**
     * Returns the nth node.
     *
     * @param $position The position of the node with 1 representing the first node.
     */
    public function nth(int $position): null|Node
    {
        $nodes = $this->toArray();

        if (!isset($nodes[$position - 1])) {
            return null;
        }

        return $nodes[$position - 1];
    }

    /**
     * Returns the last node.
     */
    public function last(): null|Node
    {
        $nodes = $this->toArray();

        return array_last($nodes);
    }

    /**
     * Checks if the nodes include a node of the given name.
     *
     * @param $name The node name.
     */
    public function has(string $name): bool
    {
        foreach ($this as $node) {
            if ($node->is($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the first node has the given name.
     *
     * @param $name The node name.
     */
    public function hasFirst(string $name): bool
    {
        if ($first = $this->first()) {
            return $first->is($name);
        }

        return false;
    }

    /**
     * Checks if the last node has the given name.
     *
     * @param $name The node name.
     */
    public function hasLast(string $name): bool
    {
        if ($last = $this->last()) {
            return $last->is($name);
        }

        return false;
    }

    /**
     * Check if the nodes include as node of any of the given names.
     *
     * @param $names The node names.
     */
    public function hasAnyOf(array $names): bool
    {
        foreach ($this as $node) {
            if ($node->isAnyOf($names)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Accepts or rejects nodes while filtering. The method applies optional
     * constraints like limit and offset or content-based filters.
     */
    public function accept(): bool
    {
        // finished indicates whether an offset or limit has already been reached,
        // this will be reset on rewind
        if ($this->finished === true) {
            return false;
        }

        if ($this->current()->isDetached()) {
            return false;
        }

        $this->accepted = true;

        // Apply filters
        $filters = [
            'applySequence',
            'applyLimit',
            'applyOffset',
            'applyFilters'
        ];
        while ($this->accepted === true && !empty($filters)) {
            $check = array_shift($filters);
            $this->{$check}();
        }

        // only count accepted nodes
        if ($this->accepted) {
            $this->position++;
        }

        return $this->accepted;
    }

    /**
     * Applies templates to nodes if node and template name match. Nodes will be
     * replaced with the returned result of the matching templates. By default,
     * the templates will be provided with `$node` (Node object),
     * `$text` (string of text content), `$content` (string of html content) and
     * `$html` (Inspector) variables.
     *
     * @param $path The template path.
     * @param $data Additional data passed to the template.
     */
    public function apply(string $path, array $data = []): self
    {
        $files = new DirectoryIterator($path);
        $templates = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $templates[$file->getBasename('.php')] = $file->getRealPath();
            }
        }

        foreach ($this as $node) {
            if ($node->isText()) {
                continue;
            }

            $node->apply($templates, $data);
        }

        return $this;
    }

    /**
     * Applies a callback to each node in the collection.
     *
     * @param $callback description The callback.
     */
    public function each(Closure $callback): self
    {
        foreach ($this as $node) {
            $callback($node);
        }

        return $this;
    }

    /**
     * Wraps all nodes in the collection in an element.
     *
     * @param $name The node name.
     * @param $text The node text content.
     * @param $attributes The node attributes.
     */
    public function wrap($name, string $text = '', array $attributes = []): self
    {
        $this->rewind();

        if (!$this->current()) {
            return $this;
        }

        $wrapper = $this->current()->createElement($name, $text, $attributes);
        $this->current()->before($wrapper);

        foreach ($this as $node) {
            $wrapper->append($node);
        }

        return $this;
    }

    /**
     * Applies a method to all nodes in the collection.
     *
     * @param $name The method name.
     * @param $arguments The method arguments.
     */
    public function __call(
        string $name,
        array $arguments
    ): array|bool|string|self {
        $innerIterator = $this->getInnerIterator();
        if ($this->lastIndex === -1) {
            return $this;
        }

        $first = $innerIterator[0];

        if (method_exists($first, $name)) {
            $method = new \ReflectionMethod($first, $name);

            if ($method->isPublic()) {
                $results = [];

                foreach ($this as $node) {
                    $results[] = $node->{$name}(...$arguments);
                }

                $types = explode('|', strval($method->getReturnType()));

                if (in_array('array', $types) || in_array('Words', $types)) {
                    $combined = [];

                    foreach ($results as $result) {
                        array_push($combined, ...$result);
                    }

                    return $combined;
                }

                if (in_array('bool', $types)) {
                    $combined = false;

                    foreach ($results as $result) {
                        if ($result === true) {
                            $combined = true;
                            break;
                        }
                    }

                    return $combined;
                }

                if (in_array('int', $types) || in_array('float', $types)) {
                    return $results[0] ?? 0;
                }

                if (in_array('string', $types)) {
                    return \implode('', $results) ?? '';
                }
            }
        } else {
            throw new BadMethodCallException(
                "Call to unknown method $name on Nodes."
            );
        }

        // enable method chaining
        return $this;
    }

    /**
     * Converts all nodes to HTML.
     */
    public function toHtml(): string
    {
        $html = '';

        foreach ($this as $node) {
            $html .= $node->toHtml();
        }

        return trim($html);
    }

    /**
     * Returns the nodes as array.
     */
    public function toArray(): array
    {
        return iterator_to_array($this, false);
    }

    /**
     * Returns the parent `HtmlDocument` of the nodes.
     */
    public function toDocument(): HtmlDocument|null
    {
        if (empty($this)) {
            return null;
        }

        return $this[0]->document();
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
