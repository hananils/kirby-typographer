<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

class Primes extends Correction
{
    /**
     * The primes corrector should be run after quotes have been parsed. It
     * takes any left straight quotes that are left and are following a number
     * and converts them to primes.
     */
    private $replacements = [
        "/(\d)(''|\")/im" => '$1″',
        "/(\d)'/im" => '$1′'
    ];

    public function apply()
    {
        $this->toFormattableTexts()->replaceTextAll(
            $this->replacements,
            isRegEx: true
        );
    }
}
