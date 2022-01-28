<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Ellipsis extends Correction
{
    public $replacements = [
        '/\.\.\./' => '…'
    ];
}
