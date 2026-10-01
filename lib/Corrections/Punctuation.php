<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

class Punctuation extends Correction
{
    private $replacements = [
        '/(\p{Zs})+(;|:|!|\?)/uim' => Correction::NARROW_NO_BREAK_SPACE . '$2'
    ];

    public function apply()
    {
        if ($this->locale->matches('fr')) {
            // Only keep elements with no additional or French language setting
            $locales = ['', 'fr', 'fr_FR'];
        } else {
            // Only keep elements with French language settings
            $locales = ['fr', 'fr_FR'];
        }

        $this
            ->toFormattableTexts()
            ->filterBy('@lang', 'in', $locales)
            ->replaceTextAll($this->replacements, isRegEx: true);
    }
}
