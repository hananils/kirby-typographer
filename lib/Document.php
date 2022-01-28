<?php

namespace Hananils;

use DomDocument;
use DomXPath;

class Document
{
    protected $document;
    protected $xpath;
    protected $query;

    public function __construct()
    {
        $this->document = new DomDocument();
        $this->xpath = new DOMXPath($this->document);
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

        $nodes = $this->xpath->query('//body/' . $query);

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

    public function setName($query, $name)
    {
        $nodes = $this->xpath->query('//' . $query);

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

    public function level($level = 2)
    {
        $level = intval($level);

        if ($level > 1) {
            for ($i = 6; $i > 0; $i--) {
                $this->setName('h' . $i, 'h' . min($i + $level - 1, 6));
            }
        }

        return $this;
    }

    public function filter($query)
    {
        $this->query = $query;

        return $this;
    }

    public function document()
    {
        return $this->document;
    }
}
