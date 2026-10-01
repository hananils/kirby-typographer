<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Paragraphs which are shorter than a single line might benefit from dedicated
 * styling like customized indent or margin. Typographer will add a class to
 * those short paragraphs, based on the defined threshold.
 */
class ShortParagraphs extends Correction
{
    public function apply()
    {
        $max = $this->options->get('maxlength', 75);
        $classname = $this->options->get('classname', 'short');

        $this
            ->toFormattables()
            ->filterBy('name', '==', 'p')
            ->each(function ($paragraph) use ($max, $classname) {
                if (strlen($paragraph->getText()) < $max) {
                    $paragraph->addClass($classname);
                }
            });
    }
}
