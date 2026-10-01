<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Ellipsis finds three consicutive dots and replaces them with the ellipsis
 * character.
 *
 * @extends Correction
 */
class Ellipsis extends Correction
{
    private $replacements = [
        '/\.\.\./' => '…'
    ];

    /**
     * Formats ellipsis, excluding preformatted text.
     *
     * This method is called when applying `$typographer->ellipsis()`.
     */
    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
