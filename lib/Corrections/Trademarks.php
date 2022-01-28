<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Trademarks extends Correction
{
    public $replacements = [
        '/\(tm\)/i' => '™',
        '/\(r\)/i' => '®',
        '/\(c\)\p{Zs}(\d{4,})/ui' => '©&nbsp;$1',
        '/\(c\)/i' => '©'
    ];
}
