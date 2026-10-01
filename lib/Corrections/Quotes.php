<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Document\Nodes;
use Hananils\Typographer\Correction;
use Hananils\Typographer\Enums\QuoteStyle;
use Hananils\Typographer\Enums\Wildcards;
use Hananils\Typographer\Locale;

/**
 * Quotes finds straight double and single quotation marks and replaces them with
 * their typographic counterparts. Typographic quotation marks are adapted based
 * on the current locale of the content. It's possible to switch to alternative
 * sets if this is supported by the current language.
 */
class Quotes extends Correction
{
    private Nodes $texts;

    private Wildcards $wildcard;
    private string $openingQuote;
    private string $closingQuote;
    private array $omissions = [];

    private int $opening = 0;
    private int $closing = 0;
    private array $occurences = [];

    /** @var $style The quote style applied to the text, either `default` or `alternatives`. */
    public string $style = 'default';

    public function apply()
    {
        $this->texts = $this->toFormattableTexts();

        foreach (Wildcards::cases() as $wildcard) {
            if ($this->texts->contains($wildcard->value)) {
                $this->wildcard = $wildcard;

                $this->applyLocale();
                $this->findQuotes();
                $this->replaceQuotes();
            }
        }
    }

    private function applyLocale(?Locale $locale = null): void
    {
        if ($locale === null) {
            $locale = $this->locale;
        }

        $style = QuoteStyle::tryFrom($this->options->get(
            'style',
            $this->style
        ));
        $quotes = $locale->quotes($style);
        $key = $this->wildcard->key();

        $this->openingQuote = $quotes[$key]['opening'];
        $this->closingQuote = $quotes[$key]['closing'];
        $this->omissions = $quotes['omissions'] ?? [];
    }

    private function findQuotes(): void
    {
        // Maximum occurences
        $count = substr_count($this->texts->getText(), $this->wildcard->value);

        $this->opening = 0;
        $this->closing = 0;
        $this->occurences = [];

        // Store quote replacement characters
        foreach ($this->texts as $text) {
            if (count($this->occurences) === $count) {
                // Stop searching if all occurences have been found
                break;
            }

            // Reset quotes state with each block start
            if ($text->isBlockStart()) {
                // Last block has an opening quote that has not been closed
                if ($this->opening > $this->closing) {
                    // Reset last opening quote because there is no closing partner
                    $previous = $this->findPreviousOpening();
                    $this->occurences[$previous] = $this->wildcard->value;
                }

                $this->opening = 0;
                $this->closing = 0;
            }

            if (!$text->contains($this->wildcard->value)) {
                continue;
            }

            // Set locale quotes
            $locale = Locale::from($text, $this->locale->get());
            $this->applyLocale($locale);

            foreach ($text->toCharacters() as $character) {
                /**
                 * Handle non-quoting characters
                 */
                if (
                    !$character->is($this->wildcard->value)
                    || $character->inWord()
                ) {
                    // Register existing typographic quotes
                    if ($character->is($this->openingQuote)) {
                        $this->opening++;
                    } elseif ($character->is($this->closingQuote)) {
                        $this->closing++;
                    }

                    continue;
                }

                /**
                 * Handle opening characters
                 */
                if ($character->hasPrevBoundary()) {
                    if ($this->isStartOmission($character)) {
                        // Character is an omitting apostrophe
                        $this->storeNone();

                        continue;
                    } elseif ($this->opening === $this->closing) {
                        // Character is opening quote
                        $this->storeOpeningQuote();

                        continue;
                    }
                }

                /**
                 * Handle closing characters
                 */
                if ($character->hasNextBoundary()) {
                    if ($this->isEndOmission($character)) {
                        // Character is an omitting apostrophe
                        $this->storeNone();

                        continue;
                    } elseif ($this->opening > $this->closing) {
                        // Character is closing quote
                        $this->storeClosingQuote();

                        continue;
                    }
                }

                // Character is not a quote
                $this->storeNone();
            }
        }
    }

    public function isStartOmission($character)
    {
        if (!$this->omissions || $this->wildcard->value !== "'") {
            // Neither does the current locale specify any omission nor
            // does the wildcard represent a possible apostrophe.
            return false;
        }

        return (
            preg_match(
                $this->omissions['start'],
                (string) $character->nextWord()
            ) === 1
        );
    }

    public function isEndOmission($character)
    {
        if (!$this->omissions || $this->wildcard->value !== "'") {
            // Neither does the current locale specify any omission nor
            // does the wildcard represent a possible apostrophe.
            return false;
        }

        return (
            preg_match($this->omissions['end'], (string) $character->prevWord())
            === 1
        );
    }

    /**
     * Replace quotes throughout the content.
     */
    public function replaceQuotes()
    {
        foreach ($this->texts as $text) {
            if (empty($this->occurences)) {
                // Stop parsing when all occurences have been replaced
                break;
            }

            $result = '';

            foreach ($text->toCharacters() as $character) {
                if (
                    $character->is($this->wildcard->value)
                    && !$character->inWord()
                ) {
                    $result .= array_shift($this->occurences);
                } else {
                    $result .= $character;
                }
            }

            // Set locale quotes
            $locale = Locale::from($text, $this->locale->get());
            $this->applyLocale($locale);

            // Clean-up spaces
            $result = preg_replace(
                '/' . $this->openingQuote . '\p{Zs}/',
                $this->openingQuote,
                $result
            );
            $result = preg_replace(
                '/\p{Zs}' . $this->closingQuote . '/',
                $this->closingQuote,
                (string) $result
            );

            // Set text
            $text->setText($result);
        }
    }

    private function storeOpeningQuote()
    {
        $this->occurences[] = $this->openingQuote;
        $this->opening++;
    }

    private function storeClosingQuote()
    {
        $this->occurences[] = $this->closingQuote;
        $this->closing++;
    }

    public function storeNone()
    {
        $this->occurences[] = $this->wildcard->value;
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
