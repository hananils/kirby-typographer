<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Dashes replaces `--` with n-dashes and `---` with m-dashes, adding non
 * breaking spaces before the dashes. It also replaces single dashes used to
 * mark ranges with their typographic counterpart.
 *
 * > This does not look right <mark>--</mark> doesn't it?
 *
 * > This looks much better <mark>–</mark> don't you think!
 */
class Dashes extends Correction
{
    private $replacements = [
        // n-dashes: single hyphen, double hyphen, n-dash
        '/ (-|--|–) /im' => Correction::NO_BREAK_SPACE . '– ',

        // m-dashes: tripple hyphen, m-dash
        '/ (---|—) /im' => Correction::NO_BREAK_SPACE . '– '
    ];

    /**
     * Formats dashes, excluding preformatted text.
     *
     * This method is called when applying `$typographer->dashes()`.
     */
    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
