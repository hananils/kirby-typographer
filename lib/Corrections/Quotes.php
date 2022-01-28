<?php

namespace Hananils\Corrections;

use Hananils\Correction;
use Hananils\Locale;

class Quotes extends Correction
{
    private $wildcard;
    private $openingQuote;
    private $closingQuote;
    private $lastClosing;

    private $opening = 0;
    private $closing = 0;
    private $occurences = [];
    private $prohibitions = [
        'prefix' => null,
        'suffix' => null
    ];

    private $blocks = [
        'address',
        'article',
        'aside',
        'blockquote',
        'dd',
        'details',
        'dialog',
        'div',
        'dl',
        'dt',
        'fieldset',
        'figcaption',
        'figure',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'header',
        'hgroup',
        'hr',
        'li',
        'main',
        'nav',
        'ol',
        'p',
        'section',
        'table',
        'ul'
    ];

    public function apply()
    {
        if ($variant = $this->option('variant')) {
            $this->locale->setVariant($variant);
        }

        $content = $this->document->documentElement->textContent;

        foreach (["'", '"'] as $quote) {
            if (strpos($content, $quote) !== false) {
                $this->wildcard = $quote;
                $this->findQuotes();
                $this->setQuotes();
            }
        }
    }

    /**
     * Read content character by character, identifying quote pairs.
     */
    private function findQuotes()
    {
        $this->opening = 0;
        $this->closing = 0;
        $this->occurences = [];

        // Store quote replacement characters
        foreach ($this->text() as $text) {
            $content = $text->textContent;

            // Reset quotes state with each block start
            if ($this->isBlockStart($text) === true) {
                // Last block has an opening quote that has not been closed
                if ($this->opening > $this->closing) {
                    // Reset last opening quote because there is no closing partner
                    $previous = $this->findPreviousOpening();
                    $this->occurences[$previous] = $this->wildcard;
                }

                $this->opening = 0;
                $this->closing = 0;
            }

            $this->findLocale($text);
            $this->getLocaleQuotes();

            foreach (mb_str_split($content) as $index => $character) {
                // Character is not a quote
                if (
                    $character !== $this->wildcard ||
                    $this->isInline($content, $index)
                ) {
                    // Register existing typographic quotes
                    if ($character === $this->openingQuote) {
                        $this->opening++;
                    } elseif ($character === $this->closingQuote) {
                        $this->closing++;
                        $this->lastClosing = $index;
                    }

                    continue;
                }

                // Set quotes
                if ($this->isOpeningQuote($text, $content, $index)) {
                    // Character is an opening quote
                    $this->occurences[] = $this->openingQuote;
                    $this->opening++;
                } elseif ($this->opening === 0) {
                    // No preceding opening quote
                    $this->occurences[] = $this->wildcard;
                } elseif ($this->closing === $this->opening) {
                    // Character is an orphaned closing quote without related opening quote
                    $this->handleOrphans($content, $index, $character);
                } else {
                    // Character is a closing quote
                    $this->occurences[] = $this->closingQuote;
                    $this->closing++;
                    $this->lastClosing = $index;
                }
            }
        }
    }

    /**
     * Determines whether the current text node is opening a new block context.
     */
    private function isBlockStart($text)
    {
        $parent = $text->parentNode;

        if (
            $text->previousSibling === null &&
            in_array($parent->localName, $this->blocks)
        ) {
            return true;
        }

        return false;
    }

    private function handleOrphans($content, $index, $character)
    {
        $currentIsQuote = false;
        $prev = $this->getPrev($content, $index);

        if ($prev === $this->wildcard) {
            // There are two consecutive quotes
            $currentIsQuote = true;
        } else {
            // Evaluate quote propabilities
            $propability = $this->contextLocale->evaluateClosingQuotes(
                [
                    [
                        'preceding' => $this->getPrecedingText(
                            $content,
                            $this->lastClosing
                        ),
                        'following' => $this->getFollowingText(
                            $content,
                            $this->lastClosing
                        )
                    ],
                    [
                        'preceding' => $this->getPrecedingText(
                            $content,
                            $index
                        ),
                        'following' => $this->getFollowingText($content, $index)
                    ]
                ],
                $this->wildcard
            );

            if ($propability[0] < $propability[1]) {
                // The previous quote is considered not to be a quote
                $currentIsQuote = true;
            }
        }

        if ($currentIsQuote === true) {
            // The previous closing quote is not a quote
            $this->occurences[$this->findPreviousClosing()] = $this->wildcard;

            // The current wildcard is a closing quote
            $this->occurences[] = $this->closingQuote;
            $this->lastClosing = $index;
        } else {
            // The current wildcard is not a quote
            $this->occurences[] = $this->wildcard;
        }
    }

    /**
     * Checks if the given character is within a word.
     */
    private function isInline($content, $index)
    {
        $prev = $this->getPrev($content, $index);
        $next = $this->getNext($content, $index);

        return preg_match('/\p{L}/u', $prev) && preg_match('/\p{L}/u', $next);
    }

    /**
     * Checks if the given character is an opening quote.
     */
    private function isOpeningQuote($text, $content, $index)
    {
        // Is first character in block
        if ($index === 0 && $text->previousSibling === null) {
            return true;
        }

        // Is nested secondary opening quote
        $prev = $this->getPrev($content, $index);
        if ($this->wildcard === "'" && $prev === '"') {
            return true;
        }

        // @todo: This might require a locale dependent check
        // if ($this->open === true) {
        //     // There is already an unclosed opening quote
        //     return false;
        // }

        // Get text context
        $preceding = $this->getPrecedingText($content, $index);
        $following = $this->getFollowingText($content, $index);

        return preg_match('/(\s|\(|-)/', $prev) &&
            $this->contextLocale->assessOpeningQuote(
                $preceding,
                $following,
                $this->wildcard
            ) >= 0;
    }

    /**
     * Replace quotes troughout the content.
     */
    public function setQuotes()
    {
        foreach ($this->text() as $text) {
            $content = $text->textContent;
            $result = '';

            foreach (mb_str_split($content) as $index => $character) {
                if (
                    $character === $this->wildcard &&
                    !$this->isInline($content, $index)
                ) {
                    $result .= array_shift($this->occurences);
                } else {
                    $result .= $character;
                }
            }

            $text->textContent = html_entity_decode($result);
        }
    }

    /**
     * Get the localized opening and closing quotes for the current context.
     */
    public function getLocaleQuotes()
    {
        if ($this->wildcard === '"') {
            $this->openingQuote = $this->contextLocale->getDoubleOpeningQuote();
            $this->closingQuote = $this->contextLocale->getDoubleClosingQuote();
        } else {
            $this->openingQuote = $this->contextLocale->getSingleOpeningQuote();
            $this->closingQuote = $this->contextLocale->getSingleClosingQuote();
        }
    }

    public function getPrev($content, $index)
    {
        if ($index > 0) {
            return mb_substr($content, $index - 1, 1);
        }

        return '';
    }

    public function getNext($content, $index)
    {
        if ($index < mb_strlen($content) - 1) {
            return mb_substr($content, $index + 1, 1);
        }

        return '';
    }

    public function getPrecedingText($content, $index)
    {
        $preceding = mb_substr($content, 0, $index);

        return mb_substr($preceding, -10);
    }

    public function getFollowingText($content, $index)
    {
        $following = mb_substr($content, $index);

        return mb_substr($following, 1, 10);
    }

    private function findLast()
    {
        return array_key_last($this->occurences);
    }

    private function findPreviousOpening()
    {
        return array_search(
            $this->openingQuote,
            array_reverse($this->occurences, true)
        );
    }

    private function findPreviousClosing()
    {
        return array_search(
            $this->closingQuote,
            array_reverse($this->occurences, true)
        );
    }
}
