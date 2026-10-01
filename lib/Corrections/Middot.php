<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

class Middot extends Correction
{
    private $replacements = [
        '/ · /im' => Correction::NO_BREAK_SPACE . '· '
    ];

    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
