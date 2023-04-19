<?php

namespace Hananils;

use DomDocument;
use DomXPath;

class Document
{
    protected $document;
    protected $query;

    public function __construct()
    {
        $this->document = new DomDocument();
    }

    protected function load($html)
    {
        $internal = libxml_use_internal_errors(true);

        if ($html = trim($html)) {
            $this->document->loadHTML('<?xml encoding="UTF-8">' . $html);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internal);
    }

    public function setAttributes($query, $attributes)
    {
        if (!$query || !$attributes) {
            return $this;
        }

        $xpath = new DOMXPath($this->document);
        $nodes = $xpath->query('//body/' . $query);

        foreach ($nodes as $node) {
            foreach ($attributes as $name => $value) {
                $node->setAttribute($name, $value);
            }
        }

        return $this;
    }

    public function setAttribute($query, $name, $value)
    {
        if (!$query || !$name || !$value) {
            return $this;
        }

        $this->setAttributes($query, [
            $name => $value
        ]);

        return $this;
    }

    public function rename($query, $name)
    {
        $xpath = new DOMXPath($this->document);
        $nodes = $xpath->query('//' . $query);

        foreach ($nodes as $node) {
            $newNode = $this->document->createElement($name);

            if ($node->attributes->length) {
                foreach ($node->attributes as $attribute) {
                    $newNode->setAttribute(
                        $attribute->nodeName,
                        $attribute->nodeValue
                    );
                }
            }

            while ($node->firstChild) {
                $newNode->appendChild($node->firstChild);
            }

            $node->parentNode->replaceChild($newNode, $node);
        }

        return $this;
    }

    public function remove($query)
    {
        $xpath = new DOMXPath($this->document);
        $nodes = $xpath->query('//' . $query);

        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }

        return $this;
    }

    public function removeEmpty()
    {
        $voids = [
            'area',
            'base',
            'br',
            'col',
            'embed',
            'hr',
            'img',
            'input',
            'link',
            'meta',
            'param',
            'source',
            'track',
            'wbr'
        ];

        $xpath = new DOMXPath($this->document);
        $nodes = $xpath->query('//*[not(normalize-space()) and not(*)]');

        foreach ($nodes as $node) {
            if (!in_array($node->localName, $voids)) {
                $node->parentNode->removeChild($node);
            }
        }

        return $this;
    }

    public function level($level = 2)
    {
        $level = intval($level);

        if ($level > 1) {
            for ($i = 6; $i > 0; $i--) {
                $this->rename('h' . $i, 'h' . min($i + $level - 1, 6));
            }
        }

        return $this;
    }

    public function offset($position = 0)
    {
        return $this->filter('/html/body/*[position() > ' . $position . ']');
    }

    public function filter($query)
    {
        $this->query = $query;

        return $this;
    }

    public function getNodes()
    {
        $nodes = null;

        if ($this->query) {
            // Filter content
            $xpath = new DOMXPath($this->document);
            $nodes = $xpath->query($this->query);
        } else {
            // Inline text is wrapped in a paragraph automatically on load.
            // There can only ever be one paragraph in the document in these
            // cases, thus taking it as the parent will return the inline content
            // needed for output.
            if ($this->flow === 'inline') {
                $parent = $this->document->getElementsByTagName('p');
            } else {
                $parent = $this->document->getElementsByTagName('body');
            }

            if ($parent && $parent->count()) {
                $nodes = $parent->item(0)->childNodes;
            }
        }

        return $nodes;
    }

    public function document()
    {
        return $this->document;
    }
}
