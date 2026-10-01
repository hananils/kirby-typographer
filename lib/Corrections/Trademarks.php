<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Typographer will convert text representation for trademark and copyright signs
 * to the correct characters. Copyright signs followed by a year will be joined
 * by a non-breaking space in order to prevent intermediate line breaks.
 *
 * @var unicode $char8482 Typographic replacement character for `(tm)`.
 * @var unicode $char174 Typographic replacement character for `(r)`.
 * @var unicode $char169 Typographic replacement character for `(c)`.
 */
class Trademarks extends Correction
{
    private $replacements = [
        '/\(tm\)/i' => '™',
        '/\(r\)/i' => '®',
        '/\(c\)\p{Zs}(\d{4,})/ui' => '©' . Correction::NO_BREAK_SPACE . '$1',
        '/©\p{Zs}(\d{4,})/ui' => '©' . Correction::NO_BREAK_SPACE . '$1',
        '/\(c\)/i' => '©'
    ];

    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
