<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class LongWords extends Correction
{
    public $wrapper = 'span';
    public $attributes = [
        'class' => 'long-word'
    ];

    /**
     * Words may contain unicode letters (\p{L}) and soft hyphens (\x{00AD}).
     */
    public $search = '/([\p{L}|\x{00AD}]{10,})/uim';
}
