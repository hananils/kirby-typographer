<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Trademarks extends Correction
{
    public $replacements = [
        '/\(tm\)/i' => '™',
        '/\(r\)/i' => '®',
        '/\(c\)\p{Zs}(\d{4,})/ui' => '©' . Correction::NO_BREAK_SPACE . '$1',
        '/\(c\)/i' => '©'
    ];
}
