<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Caps extends Correction
{
    public $wrapper = 'abbr';
    public $search = '/\b(\p{Lu}{2,})\b/u';
}
