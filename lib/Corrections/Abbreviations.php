<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * In languages like German, it's common to have multi-word abbreviation where –
 * for instance – "zum Beispiel" becomes "z. B.". The different parts of these
 * abbreviations are usually shortened with a dot and separated by a space. In
 * order to bond the parts together and to prevent breaking across multiple
 * lines, Typographer will add a narrow non-breaking space.
 *
 * > Das kann z.<mark> </mark>B. im Blocksatz ziemlich blöd aussehen.
 *
 * > So ist i.<mark> </mark>d.<mark> </mark>R. viel besser.
 *
 * @var string $char8239 Typographic replacement character for the space separating the abbreviation parts. Please note that some browsers will display this as a standard non-breaking space.
 */
class Abbreviations extends Correction
{
    private $replacements = [
        '/\b(\p{L}\.)\s?(?=\p{L}\.)/uim' =>
            '$1' . Correction::NARROW_NO_BREAK_SPACE
    ];

    /**
     * Sets narrow non breaking spaces between abbreviated words.
     *
     * This method is called when applying `$typographer->abbreviation()`.
     */
    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
