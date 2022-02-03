<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Punctation extends Correction
{
    /**
     * In French, some punctation marks should be preceded by a space, see:
     * https://fr.wikisource.org/wiki/Aide:Guide_typographique
     */
    public $locales = ['fr_FR'];
    public $replacements = [
        '/(\p{Zs})+(;|:|!|\?)/uim' => '&#8239;$2'
    ];
}
