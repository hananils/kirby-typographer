<?php

namespace Hananils;

use DOMXPath;
use Hananils\Locale;

class Correction
{
    public const NO_BREAK_SPACE = '&#160;';
    public const NARROW_NO_BREAK_SPACE = '&#8239;';
    public const NON_BREAKING_HYPHEN = '&#8209;';

    protected $document;
    protected $locale;
    protected $ignore;
    protected $options;

    private $text = null;
    protected $contextLocale;

    public $locales = [];
    public $replacements = [];
    public $search = null;
    public $wrapper;
    public $attributes = [];

    public $flow = true;

    public function __construct(
        $document,
        $locale = 'en',
        $ignore = ['pre', 'code'],
        $options = []
    ) {
        if (!is_a($locale, 'Hananils\Locale')) {
            $locale = new Locale($locale);
        }

        $this->document = $document;
        $this->locale = $locale;
        $this->ignore = $ignore;
        $this->options = $options;
    }

    public function apply()
    {
        if (!empty($this->replacements)) {
            $this->replace();
        } elseif (!empty($this->search)) {
            $this->wrap();
        }
    }

    public function replace()
    {
        $patterns = array_keys($this->replacements);
        $replacements = array_map(
            'html_entity_decode',
            array_values($this->replacements)
        );

        foreach ($this->text() as $text) {
            $this->findLocale($text);

            if (
                !empty($this->locales) &&
                !$this->contextLocale->in($this->locales)
            ) {
                continue;
            }

            $text->textContent = preg_replace(
                $patterns,
                $replacements,
                html_entity_decode($text->textContent)
            );
        }
    }

    public function wrap()
    {
        $wrapper = $this->document->createElement($this->wrapper);
        foreach ($this->attributes as $attribute => $value) {
            $wrapper->setAttribute($attribute, $value);
        }

        foreach ($this->text() as $text) {
            $content = $text->textContent;
            $parent = $text->parentNode;

            // Skip text that is already wrapped
            if ($parent->nodeName === $this->wrapper) {
                continue;
            }

            $parts = preg_split(
                $this->search,
                $content,
                -1,
                PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
            );

            foreach ($parts as $part) {
                if (preg_match($this->search, $part) === 1) {
                    $node = $wrapper->cloneNode();
                    $node->textContent = $part;
                } else {
                    $node = $this->document->createTextNode($part);
                }

                $parent->insertBefore($node, $text);
            }

            $parent->removeChild($text);
        }
    }

    public function text()
    {
        if ($this->text !== null) {
            return $this->text;
        }

        // General expression to find all text nodes
        $expression = '//text()';

        // More precise expression, if specific nodes should be ignore
        if ($this->ignore) {
            $ignore = [];
            foreach ($this->ignore as $name) {
                $ignore[] = 'ancestor::' . $name;
            }
            $expression .= '[not(' . implode(' or ', $ignore) . ')]';
        }

        // Get text nodes
        $xpath = new DOMXPath($this->document);
        $this->text = $xpath->query($expression);

        return $this->text;
    }

    public function hasFlow($flow)
    {
        // If flow is true, is allows for inline and block use
        if ($this->flow === true) {
            return true;
        }

        // Check given flow
        return $this->flow === $flow;
    }

    public function findLocale($text)
    {
        $xpath = new DOMXPath($this->document);
        $parent = $xpath->query('ancestor::*[@lang][1]', $text);

        if ($parent->length) {
            $this->contextLocale = new Locale(
                $parent->item(0)->attributes->getNamedItem('lang')->nodeValue
            );

            if ($variant = $this->option('variant')) {
                $this->contextLocale->setVariant($variant);
            }
        } else {
            $this->contextLocale = $this->locale;
        }
    }

    public function document()
    {
        return $this->document;
    }

    public function option($key, $fallback = null)
    {
        if (isset($this->options[$key]) && !empty($this->options[$key])) {
            return $this->options[$key];
        }

        return $fallback;
    }
}
