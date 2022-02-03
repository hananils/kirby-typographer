<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Abbreviations extends Correction
{
    /**
     * In German, abbreviations with dots should be separated by a thin space.
     */
    public $locales = ['de', 'de_DE', 'de_AT', 'de_CH'];
    public $replacements = [
        // apostrophes at the end of a word
        '/\b(\p{L}\.)\s?(?=\p{L}\.)/uim' =>
            '$1' . Correction::NARROW_NO_BREAK_SPACE
    ];
}
