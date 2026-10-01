<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * In order to allow for line breaks after a slash within a word, Typographer
 * will add a zero-width space after each forward or backward slash.
 *
 * @var unicode $char8203 Serves as optional line break marker.
 */
class Slashes extends Correction
{
    private $replacements = [
        // Find forward and backward slashes
        '/(\p{L})([\/\\\])(\p{L})/uim' => '$1$2&#8203;$3'
    ];

    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
