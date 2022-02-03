<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Apostrophes extends Correction
{
    /**
     * Apostrophes at the end of a word can be confused with closing single
     * quotes. This corrector should be run after parsing quotes.
     */
    public $replacements = [
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
}
