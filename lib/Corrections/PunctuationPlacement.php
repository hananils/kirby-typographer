<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

class PunctuationPlacement extends Correction
{
    public function apply()
    {
        $this->toFormattableTexts()->each(function ($text) {
            if ($text->isBlockStart()) {
                // Punctation at the beginning of a block should not be moved
                return;
            }

            $content = $text->getText();

            // Move punctation inside preceding inline element
            if (preg_match('/^(\pP)/', $content, $matches) === 1) {
                $punctuation = $matches[1];

                if ($prev = $text->prev()) {
                    if (!$prev->isElement()) {
                        return;
                    }

                    $span = $text->document()->createElement(
                        'span',
                        htmlspecialchars($punctuation)
                    );
                    $span->setAttribute('class', 'moved-punctuation');
                    $prev->append($span);

                    $text->setText(substr($content, strlen($punctuation)));
                }
            }
        });
    }
}
