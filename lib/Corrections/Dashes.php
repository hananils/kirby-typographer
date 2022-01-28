<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Dashes extends Correction
{
    public $replacements = [
        // n-dashes
        '/ - /im' => '&nbsp;– ',

        // m-dashes
        '/ -- /im' => '&nbsp;— ',

        // ranges
        '/(\d)-(\d)/im' => '$1–$2'
    ];
}
