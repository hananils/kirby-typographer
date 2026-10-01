<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Document\Nodes;
use Hananils\Typographer\Correction;

/**
 * In typescript, apostrophes are often represented by a straight apostrophe
 * character whereas typographically a right single quotation mark is
 * perferred. Typographer will find apostrophes and replace them with their
 * typographic counterpart.
 *
 * In order to prevent mismatches with actual single quotes, this correction
 * should be run after parsing quotes.
 *
 * @var string $char8217 Typographic replacement character a straigh `'` apostrophe.
 */
class Apostrophes extends Correction
{
    private $replacements = [
        // apostrophes at the beginning of a word
        "/(\p{Zs})'(\p{L})/uim" => '$1’$2',

        // apostrophes within words
        "/(\p{L})'(\p{L})'(\p{L})/uim" => '$1’$2’$3',
        "/(\p{L})'(\p{L})/uim" => '$1’$2',

        // apostrophes at the end of a word
        "/(\p{L})'(\p{Zs}|\p{P})/uim" => '$1’$2',

        // special case: shortened "and"
        "/ 'n' /im" => ' ’n’ ',
        "/'n'/im" => '’n’',

        // shortened decades: the "’80"
        "/'(\d0)/im" => '’$1'
    ];

    /**
     * Applies typographic apostrophes, ignoring preformatted text.
     *
     * This method is called when applying `$typographer->apostrophes()`.
     */
    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
