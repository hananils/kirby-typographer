<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Currencies finds numbers with currency symbols and adds a narrow non breaking
 * space between number and symbol.
 */
class Currencies extends Correction
{
    private $replacements = [
        '/(\p{Sc})(\d)/u' => '$1&#8239;$2',
        '/(\d)(\p{Sc})/u' => '$1&#8239;$2'
    ];

    /**
     * Adds spaces between numbers and currency symbols, excluding preformatted
     * text.
     *
     * This method is called when applying `$typographer->currencies()`.
     */
    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
