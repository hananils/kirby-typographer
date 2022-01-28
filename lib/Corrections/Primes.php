<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Primes extends Correction
{
    /**
     * The primes corrector should be run after quotes have been parsed. It
     * takes any left straight quotes that are left and are following a number
     * and converts them to primes.
     */
    public $replacements = [
        "/(\d)(''|\")/im" => '$1″',
        "/(\d)'/im" => '$1′'
    ];
}
