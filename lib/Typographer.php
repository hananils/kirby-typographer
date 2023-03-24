<?php

namespace Hananils;

use DomDocument;
use DOMXPath;
use Kirby\Toolkit\Str;
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
    protected $ignore = ['pre', 'code', 'script', 'style'];
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
        'caps' => '\Hananils\Corrections\Caps',
        'lists' => '\Hananils\Corrections\Lists'
    ];

    public function __construct($locale = 'en', $flow = 'block')
    {
        parent::__construct();

        $this->locale = new Locale($locale);

        if ($flow === 'inline') {
            $this->flow = 'inline';
        }
    }

    public function __call($name, $arguments)
    {
        // Correction
        if (array_key_exists($name, $this->corrections)) {
            $this->apply($this->corrections[$name]);
            $this->isCorrected = true;
        }

        return $this;
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }

    public function parse($input)
    {
        $this->load($input);
        $this->isCorrected = false;

        return $this;
    }

    public function ignore(array $ignore = [])
    {
        $this->ignore = $ignore;

        return $this;
    }

    public function corrections(array $corrections = [])
    {
        foreach ($corrections as $id => $correction) {
            // Convert numeric ids to string so that correction can be
            // called directly via magic method.
            if (is_numeric($id)) {
                $id = lcfirst(array_pop(explode('/', $string)));
            }

            $this->corrections[$id] = $correction;
        }

        return $this;
    }

    public function options(array $options = [])
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function alternatives($alternatives = true)
    {
        $this->options = ['alternatives' => $alternatives];

        return $this;
    }

    private function process()
    {
        if ($this->isCorrected === false) {
            if ($this->document->childNodes->length > 0) {
                foreach ($this->corrections as $correction) {
                    $this->apply($correction);
                }
            }
        }
    }

    private function apply($correction)
    {
        $method = new $correction(
            $this->document,
            $this->locale,
            $this->ignore,
            $this->options
        );

        if ($method->hasFlow($this->flow)) {
            $method->apply();

            $this->document = $method->document();
        }

        $this->isCorrected = true;
    }

    private function getNodes()
    {
        $nodes = null;

        if ($this->query) {
            // Filter content
            $xpath = new DOMXPath($this->document);
            if (strpos($this->query, '/') === 0) {
                $nodes = $xpath->query($this->query);
            } else {
                $nodes = $xpath->query('//' . $this->query);
            }
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

    /**
     * Status checks
     */

    public function isCorrected()
    {
        return $this->isCorrected === true;
    }

    public function isFiltered()
    {
        return isset($this->query);
    }

    public function isInline()
    {
        return $this->flow === 'inline';
    }

    public function isBlock()
    {
        return $this->flow === 'block';
    }

    /**
     * Converters
     */

    public function toHtml()
    {
        // Apply typography
        $this->process();

        // Get nodes
        $nodes = $this->getNodes();

        // Get typographically corrected content
        $content = '';
        if ($nodes) {
            foreach ($nodes as $node) {
                $content .= $this->document->saveHTML($node);
            }
        }

        return $content;
    }

    public function toText($boundary = null, $ellipsis = '&nbsp;…')
    {
        // Handle empty documents
        if (!trim($this->document->textContent)) {
            return '';
        }

        // Get nodes
        $nodes = $this->getNodes();

        // Get typographically corrected content
        $text = '';
        foreach ($nodes as $node) {
            // Collapse whitespace
            $content = trim(
                preg_replace('/(\r|\n|\t|\p{Zs})+/', ' ', $node->textContent)
            );

            if (!$content) {
                continue;
            }

            $text .= ' ' . $content;
        }

        // Shorten text if required
        if ($boundary > 0) {
            $words = explode(' ', $text);
            $text = '';
            $length = -1;

            foreach ($words as $word) {
                $length = $length + mb_strlen($word) + 1;

                if ($length > $boundary) {
                    break;
                }

                $text .= ' ' . $word;
            }
        }

        // Apply typography
        $this->parse($text);
        $this->process();

        // Trim text
        $text = trim($this->document->textContent);

        // Shorten text if required
        if ($boundary > 0) {
            // Remove orphaned punctation
            $text = trim($text, '-–,;:');
            $text .= $ellipsis;

            // Remove orphaned words after shortening,
            // e. g. removes "It …" from "The summary. It …"
            $text = preg_replace(
                '/([.?!])\p{Zs}\p{L}+' . $ellipsis . '$/uim',
                '$1',
                $text
            );
        }

        return $text;
    }

    public function toArray()
    {
        return [
            'locale' => $this->locale->toString(),
            'ignore' => $this->ignore,
            'flow' => $this->flow,
            'corrections' => array_keys($this->corrections),
            'options' => $this->options,
            'corrected' => $this->isCorrected(),
            'filtered' => $this->isFiltered()
        ];
    }

    public function toDocument()
    {
        return $this->document;
    }

    public function __toString()
    {
        return $this->toHtml();
    }
}
