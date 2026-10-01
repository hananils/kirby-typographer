<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

class Math extends Correction
{
    private $replacements = [
        // times sign
        '/(\d) x (\d)/' => '$1' . Correction::NO_BREAK_SPACE . '× $2'
    ];

    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
