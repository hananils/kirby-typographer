<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Ranges replaces single dashes used to mark ranges with their typographic
 * counterpart.
 */
class Ranges extends Correction
{
    private $replacements = [
        // ranges
        '/(?<![-+])\b(\d+)-(\d+)\b(?![-+])/uim' => '$1–$2'
    ];

    /**
     * Formats ranges, excluding preformatted text.
     *
     * This method is called when applying `$typographer->ranges()`.
     */
    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
