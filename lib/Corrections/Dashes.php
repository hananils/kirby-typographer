<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Dashes extends Correction
{
    public $replacements = [
        // n-dashes: single hyphen, double hyphen, n-dash
        '/ (-|--|–) /uim' => Correction::NO_BREAK_SPACE . '– ',

        // m-dashes: tripple hyphen, m-dash
        '/ (---|—) /uim' => Correction::NO_BREAK_SPACE . '— ',

        // ranges
        '/(\d)-(\d)/uim' => '$1–$2'
    ];
}
