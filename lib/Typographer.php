<?php

namespace Hananils;

use DomDocument;
use DOMXPath;
use Hananils\Locale;

use Hananils\Corrections\Abbreviations;
use Hananils\Corrections\Apostrophes;
use Hananils\Corrections\Caps;
use Hananils\Corrections\Dashes;
use Hananils\Corrections\Ellipsis;
use Hananils\Corrections\LongWords;
use Hananils\Corrections\Math;
use Hananils\Corrections\Primes;
use Hananils\Corrections\Punctation;
use Hananils\Corrections\Quotes;
use Hananils\Corrections\Trademarks;
use Hananils\Corrections\Widont;

class Typographer extends Document
{
    protected $locale;
    protected $ignored = ['pre', 'code'];
    protected $options = [];
    protected $flow = 'block';

    private $isCorrected = false;
    private $corrections = [
        'dashes' => '\Hananils\Corrections\Dashes',
        'math' => '\Hananils\Corrections\Math',
        'quotes' => '\Hananils\Corrections\Quotes',
        'apostrophes' => '\Hananils\Corrections\Apostrophes',
        'primes' => '\Hananils\Corrections\Primes',
        'punctation' => '\Hananils\Corrections\Punctation',
        'ellipsis' => '\Hananils\Corrections\Ellipsis',
        'trademarks' => '\Hananils\Corrections\Trademarks',
        'widont' => '\Hananils\Corrections\Widont',
        'abbreviations' => '\Hananils\Corrections\Abbreviations',
        'longWords' => '\Hananils\Corrections\LongWords',
        'caps' => '\Hananils\Corrections\Caps'
    ];

    public function __construct($locale = null)
    {
        parent::__construct();

        $this->setLocale($locale);
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->corrections)) {
            $this->apply($this->corrections[$name]);
        }

        $this->isCorrected = true;

        return $this;
    }

    public function parse($input)
    {
        $this->load($input);
        $this->isCorrected = false;

        return $this;
    }

    private function correct()
    {
        // Run correctors
        if ($this->document->childNodes->length > 0) {
            foreach ($this->corrections as $correction) {
                $this->apply($correction);
            }
        }

        $this->isCorrected = true;
    }

    private function apply($correction)
    {
        $method = new $correction(
            $this->document,
            $this->locale,
            $this->ignored,
            $this->options
        );

        if ($method->hasFlow($this->flow)) {
            $method->apply();

            $this->document = $method->document();
        }
    }

    public function toString()
    {
        // Handle empty documents
        if (!trim($this->document->textContent)) {
            return '';
        }

        // Apply typography
        if ($this->isCorrected === false) {
            $this->correct();
        }

        if ($this->query) {
            $this->xpath = new DOMXPath($this->document);
            $nodes = $this->xpath->query('//' . $this->query);
        } else {
            // Get content parent
            $parent = 'body';
            if ($this->flow === 'inline') {
                // Inline text is wrapped in a paragraph automatically on load.
                // There can only ever be one paragraph in the document in these
                // cases, thus taking it as the parent will return the inline content
                // needed for output.
                $parent = 'p';
            }

            $nodes = $this->document->getElementsByTagName($parent)->item(0)
                ->childNodes;
        }

        // Get typographically corrected content
        $content = '';
        foreach ($nodes as $node) {
            $content .= $this->document->saveHTML($node);
        }

        return $content;
    }

    public function setLocale($locale = 'en-US')
    {
        $this->locale = new Locale($locale);
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setFlow($flow = 'block')
    {
        $this->flow = $flow;
    }

    public function getFlow()
    {
        return $this->flow;
    }

    public function setIgnored(array $ignored = [])
    {
        $this->ignored = $ignored;
    }

    public function getIgnored()
    {
        return $this->ignored;
    }

    public function setCorrections(array $corrections = [])
    {
        $this->corrections = $corrections;
    }

    public function getCorrections()
    {
        return $this->corrections;
    }

    public function setOptions(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
