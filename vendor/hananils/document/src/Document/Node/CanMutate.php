<?php

namespace Hananils\Document\Node;

use Dom\AdjacentPosition;
use Dom\Node as DomNode;
use Hananils\Document\Elements;
use Hananils\Document\Inspector;
use Hananils\Document\Node;

trait CanMutate
{
    /**
     * Gets the text content of the node.
     */
    public function getText(): string
    {
        return $this->item->textContent;
    }

    /**
     * Sets the text content of the node.
     *
     * @param string $value The text value.
     */
    public function setText(string $value): self
    {
        $this->item->textContent = html_entity_decode($value);

        return $this;
    }

    /**
     * Convenience method that either sets or gets the text content of the node.
     *
     * @param string $value The text value, if used as setter.
     */
    public function text(string $value = ''): self|string
    {
        if (empty($value)) {
            return $this->item->textContent;
        }

        $this->item->textContent = $value;

        return $this;
    }

    /**
     * Given a Node representation or Dom\Node, always returns a Dom\Node.
     *
     * @param $node The node object.
     */
    private function ensureDomNode(Node|DOMNode $node): DOMNode|null
    {
        if (is_a($node, 'Hananils\Document\Node')) {
            return $node->item();
        }

        return $node;
    }

    /**
     * Inserts a node before the current one.
     *
     * @param $newItem The node to insert.
     */
    public function before(Node|DOMNode $newItem): self
    {
        $this->item->before($this->ensureDomNode($newItem));

        return $this;
    }

    /**
     * Inserts a node after the current one.
     *
     * @param $newItem The node to insert.
     */
    public function after(Node|DOMNode $newItem): self
    {
        $this->item->after($this->ensureDomNode($newItem));

        return $this;
    }

    /**
     * Prepends a node to the current child nodes.
     *
     * @param $newItem The node to insert.
     */
    public function prepend(Node|DOMNode $newItem): self
    {
        $this->item->prepend($this->ensureDomNode($newItem));

        return $this;
    }

    /**
     * Appends a node to the current child nodes.
     *
     * @param $newItem The node to insert.
     */
    public function append(Node|DOMNode $newItem): self
    {
        $this->item->append($this->ensureDomNode($newItem));

        return $this;
    }

    /**
     * Replaces the current node.
     *
     * @param $newItem The replacement node.
     */
    public function replace(Node|DOMNode $newItem): self
    {
        if ($this->isAttached()) {
            $newItem = $this->ensureDomNode($newItem);
            $this->item->parentNode->replaceChild($newItem, $this->item);
            $this->item = $newItem;
        }

        return $this;
    }

    /**
     * Searches and replaces within the node's text content.
     *
     * @param $search The search string or regular expression.
     * @param $replace The replacement string.
     * @param $isRegEx Flag whether the search is string-based or a regular expression.
     */
    public function replaceText($search, $replace, $isRegEx = false): self
    {
        if ($this->isElement()) {
            return $this->traverseText('replaceText', [
                $search,
                $replace,
                $isRegEx
            ]);
        }

        if (!$isRegEx) {
            $search = $this->stringToPattern($search);
        }

        $this->setText(
            preg_replace($search, (string) $replace, $this->getText())
        );

        return $this;
    }

    /**
     * Executes multiple search and replace actions defined as array, with the
     * keys representing the searches and the values representing the
     * replacements.
     *
     * @param $replacements The array of searches and replacements.
     * @param $isRegEx Flag whether the search is string-based or a regular expression.
     */
    public function replaceTextAll(array $replacements, $isRegEx = false): self
    {
        if ($this->isElement()) {
            return $this->traverseText('replaceTextAll', [
                $replacements,
                $isRegEx
            ]);
        }

        $text = $this->getText();

        foreach ($replacements as $search => $replace) {
            if (!$isRegEx) {
                $search = $this->stringToPattern($search);
            }

            $text = preg_replace($search, (string) $replace, (string) $text);
        }

        $this->setText($text);

        return $this;
    }

    /**
     * Finds text by pattern.
     *
     * @param $pattern The search pattern.
     */
    public function findText(string $pattern): string
    {
        preg_match($pattern, $this->getText(), $matches);

        return $matches[1] ?? '';
    }

    /**
     * Finds text by pattern, removes it from the current node and returns it.
     *
     * @param $pattern The search pattern.
     */
    public function extractText(string $pattern): string
    {
        $text = $this->findText($pattern);

        $this->replaceText($pattern, '', true);

        return $text;
    }

    /**
     * Renames the current node.
     *
     * @param $name The new name.
     */
    public function rename(string $name): self
    {
        $this->item->rename($this->item->namespaceURI, $name);

        return $this;
    }

    /**
     * Removes the current node.
     */
    public function remove(): self
    {
        if ($this->isAttached()) {
            $this->item()->parentNode->removeChild($this->item());
            $this->item = null;
        }

        return $this;
    }

    /**
     * Updates the base level for all headlines, changing the subsequent
     * hierachy accordingly. For example, if the level is set to `3`, all `h1`
     * will be updated to `h3, all `h2` to `h4` and so on.
     *
     * @param $level The base headline level.
     */
    public function level(int $level = 1): self
    {
        $headings = Elements::byCategory('heading');

        if ($this->isAnyOf($headings)) {
            $currentLevel = intval(trim($this->name(), 'h'));
            $newLevel = min(6, $currentLevel - 1 + $level);

            $this->rename('h' . $newLevel);
        }

        return $this;
    }

    /**
     * Collapses whitespace.
     *
     * @param $ignore Ignored element categories.
     */
    public function collapse(array $ignore = []): self
    {
        $texts = $this->query('descendant-or-self::text()', $this->item);

        if ($ignore !== []) {
            $texts->filterBy(
                'categoriesIncludingAncestors',
                'excludes',
                $ignore
            );
        }

        foreach ($texts as $text) {
            $text->item()->textContent = preg_replace(
                '/[ \r\n\t]+/',
                ' ',
                (string) $text->item()->textContent
            );
        }

        return $this;
    }

    /**
     * Unwraps the content of an element, keeping the child nodes, discarding
     * the current node.
     */
    public function unwrap(): self
    {
        if ($this->isElement() && $this->isAttached()) {
            foreach ([...$this->item->childNodes] as $node) {
                $this->item->before($node);
            }

            $this->remove();
        }

        return $this;
    }

    /**
     * Finds text by pattern and wraps it in an element.
     *
     * @param $pattern The search pattern.
     * @param $name The element name.
     * @param $attributes The element attributes.
     * @param $isRegEx Flag whether the pattern is string-based or a regular expression.
     */
    public function wrapText(
        string $pattern,
        string $name,
        null|array $attributes = null,
        bool $isRegEx = false
    ) {
        if ($this->isElement()) {
            return $this->traverseText('wrapText', [
                $pattern,
                $name,
                $attributes,
                $isRegEx
            ]);
        }

        if (!$isRegEx) {
            $pattern = $this->stringToPattern($pattern, withCaptureGroup: true);
        }

        if (preg_match_all($pattern, $this->getText(), $matches)) {
            $parts = preg_split(
                $pattern,
                (string) $this->item()->textContent,
                -1,
                PREG_SPLIT_DELIM_CAPTURE
            );

            $wrapped = array_map(function ($part) use (
                $matches,
                $name,
                $attributes
            ) {
                if (in_array($part, $matches[0])) {
                    $node = $this->document()->createElement($name);
                    $node->textContent = htmlspecialchars($part);

                    if ($attributes) {
                        foreach ($attributes as $name => $value) {
                            $node->setAttribute($name, $value);
                        }
                    }

                    return $node;
                }

                return $part;
            }, $parts);

            $this->item->replaceWith(...$wrapped);
        }

        return $this;
    }

    /**
     * Finds text by pattern but only wraps it if it's not wrapped by an element
     * already.
     *
     * @param $pattern The search pattern.
     * @param $name The element name.
     * @param $attributes The element attributes.
     * @param $isRegEx Flag whether the pattern is string-based or a regular expression.
     */
    public function wrapTextIfNotWrapped(
        $pattern,
        string $name = 'span',
        array $attributes = [],
        bool $isRegEx = false
    ): self {
        if ($this->isElement()) {
            return $this->traverseText('wrapTextIfNotWrapped', [
                $pattern,
                $name,
                $attributes,
                $isRegEx
            ]);
        }

        if (!$isRegEx) {
            $pattern = $this->stringToPattern($pattern, withCaptureGroup: true);
        }

        $text = $this->getText();
        if (preg_match($pattern, (string) $text, $matches)) {
            if ($matches[0] !== $text) {
                return $this->wrapText($pattern, $name, $attributes, $isRegEx);
            }
        }

        return $this;
    }

    /**
     * Converts search string to a delimited regular expression.
     *
     * @param $string The search string.
     * @param $delimiter The delimiter.
     */
    private function stringToPattern(
        string $string,
        string $delimiter = '/',
        bool $withCaptureGroup = false
    ): string {
        $pattern = preg_quote($string, $delimiter);

        if ($withCaptureGroup === true) {
            $pattern = '(' . $pattern . ')';
        }

        return $delimiter . $pattern . $delimiter;
    }

    /**
     * Applies templates to nodes if node and template name match. Nodes will be
     * replaced with the returned result of the matching templates. By default,
     * the templates will be provided with `$node` (Node object),
     * `$text` (string of text content), `$content` (string of html content) and
     * `$html` (Inspector) variables.
     *
     * @param $templates The templates.
     * @param $data Additional data passed to the template.
     */
    public function apply(array $templates = [], array $data = [])
    {
        $name = $this->name();

        if (!isset($templates[$name])) {
            return $this;
        }

        extract($data);

        // Set template variables
        $node = $this;
        $text = $node->text();
        $content = $node->content();
        $html = Inspector::fromString($node->toHtml());

        ob_start();
        include $templates[$name];
        $output = ob_get_contents();
        ob_end_clean();

        $this->item->insertAdjacentHTML(AdjacentPosition::BeforeBegin, $output);
        $this->remove();

        return $this;
    }

    /**
     * Creates a new element in the current document.
     *
     * @param $element The element name.
     * @param $text The element text content.
     * @param $attributes The element attributes.
     */
    public function createElement(
        string $element,
        string $text = '',
        array $attributes = []
    ): Node {
        $node = $this->document()->createElement($element);
        $node->textContent = $text;

        foreach ($attributes as $attribute => $value) {
            $node->setAttribute($attribute, $value);
        }

        return new Node($node);
    }
}
