<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Math extends Correction
{
    public $replacements = [
        // times sign
        '/ x /' => '&nbsp;× '
    ];
}
