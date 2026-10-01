<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Long words wraps words longer than the specified limit. This can be used to
 * apply hyphenation only to those longer word, making hyphenated text in older
 * browsers more readable.
 *
 * @extends Correction
 */
class LongWords extends Correction
{
    public function apply()
    {
        $length = $this->options->get('length', 15);
        $force = $this->options->get('force', false);
        $wrapper = $this->options->get('wrapper', 'span');
        $classname = $this->options->get('classname', 'long-word');

        $pattern = '/([\p{L}|\x{00AD}|\p{P}]{' . $length . ',})/uim';
        $method = $force === true ? 'wrapText' : 'wrapTextIfNotWrapped';

        $this->toFormattableTexts()->{$method}(
            $pattern,
            $wrapper,
            ['class' => $classname],
            isRegEx: true
        );
    }
}
