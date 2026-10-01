<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * All caps words can be disruptive within paragraphs. Typographer will find
 * uppercase words and wrap them in a `span` element. This allows for
 * further typographic styling like changing the words to small caps using CSS.
 */
class Caps extends Correction
{
    /**
     * Wraps uppercase words in a `span` with the default class of `uppercase`.
     *
     * This method is called when applying `$typographer->caps()`.
     * The class used on the wrapping `span` can be set in the options, e. g.
     * `$typographer->caps(['classname' => 'custom'])`.
     */
    public function apply()
    {
        $classname = $this->options->get('classname', 'uppercase');
        $pattern = '/\b(\p{Lu}{2,}[\p{Lu}|\p{P}]*)\b/u';

        // Find passages of uppercase text
        $this->toFormattableTexts()->wrapTextIfNotWrapped(
            $pattern,
            'span',
            ['class' => $classname],
            isRegEx: true
        );

        $this->clearSelection()
            ->select('span.uppercase')
            ->each(function ($span) use ($classname) {
                if (str_contains($span->text(), ' ')) {
                    return;
                }

                $span->rename('abbr')->removeAttribute('class');
            });
    }
}
