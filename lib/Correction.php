<?php

namespace Hananils;

use DOMXPath;
use Hananils\Locale;

class Correction
{
    protected $document;
    protected $locale;
    protected $ignored;
    protected $options;
    protected $xpath;

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
        $ignored = ['pre', 'code'],
        $options = []
    ) {
        if (!is_a($locale, 'Hananils\Locale')) {
            $locale = new Locale($locale);
        }

        $this->document = $document;
        $this->locale = $locale;
        $this->ignored = $ignored;
        $this->options = $options;

        $this->xpath = new DOMXPath($this->document);
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
                $text->textContent
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

                $text->parentNode->insertBefore($node, $text);
            }

            $text->parentNode->removeChild($text);
        }
    }

    public function text()
    {
        if ($this->text !== null) {
            return $this->text;
        }

        // General expression to find all text nodes
        $expression = '//text()';

        // More precise expression, if specific nodes should be ignored
        if ($this->ignored) {
            $ignore = [];
            foreach ($this->ignored as $name) {
                $ignore[] = 'ancestor::' . $name;
            }
            $expression = '//text()[not(' . implode(' or ', $ignore) . ')]';
        }

        // Get text nodes
        $this->text = $this->xpath->query($expression);

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
        $parent = $this->xpath->query('ancestor::*[@lang][1]', $text);

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

    public function option($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return null;
    }
}
