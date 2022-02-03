<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Dashes extends Correction
{
    public $replacements = [
        // n-dashes
        '/ - /im' => Correction::NO_BREAK_SPACE . '– ',

        // m-dashes
        '/ -- /im' => Correction::NO_BREAK_SPACE . '– ',

        // ranges
        '/(\d)-(\d)/im' => '$1–$2'
    ];
}
