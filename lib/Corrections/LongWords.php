<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class LongWords extends Correction
{
    public $wrapper = 'span';
    public $attributes = [
        'class' => 'long-word'
    ];

    public function apply()
    {
        /**
         * Words may contain unicode letters (\p{L}) and soft hyphens (\x{00AD}).
         */
        $this->search =
            '/([\p{L}|\x{00AD}|\p{P}]{' .
            $this->option('word-length', 15) .
            ',})/uim';

        $this->wrap();
    }
}
