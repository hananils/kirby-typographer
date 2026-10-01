<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;
use Hananils\Document\Node;

class Widont extends Correction
{
    public function apply()
    {
        $texts = $this->toFormattableTexts();

        foreach ($texts as $index => $text) {
            if (
                $text->isBlockEnd() ||
                ($text->parent() === null && $text->isSiblingEnd())
            ) {
                // Remove whitespace at block end
                $text->trimEnd();

                // Inspect current block end to start
                $current = $index;
                $inspected = $text;
                while ($inspected === $text || !$inspected->isBlockStart()) {
                    if ($current < 0) {
                        break;
                    }
                    $inspected = $texts->nth($current + 1);

                    // Collapse whitespace
                    $inspected->collapse();

                    // If node contains space, set non-breaking space
                    if ($inspected->contains(' ')) {
                        $this->widont($inspected);
                        break;
                    }

                    // Move to previous text node
                    $current--;
                }
            }
        }
    }

    private function widont(Node $node)
    {
        $text = $node->getText();

        // Replace space between last word and punctuation
        $text = preg_replace_callback(
            '/(\S)\s(\S?)$/u',
            function ($matches) {
                return $matches[1] . Correction::NO_BREAK_SPACE . $matches[2];
            },
            $text
        );

        // Replace space between last two words
        $text = preg_replace_callback(
            '/(\s)(?=\S*$)(\S+)/u',
            function ($matches) {
                if (stripos($matches[2], '-')) {
                    $matches[2] = str_replace(
                        '-',
                        Correction::NON_BREAKING_HYPHEN,
                        $matches[2]
                    );
                }
                return Correction::NO_BREAK_SPACE . $matches[2];
            },
            (string) $text
        );

        // Fix non-breaking space positioning around slashes
        $text = str_replace(
            ' /' . Correction::NO_BREAK_SPACE,
            Correction::NO_BREAK_SPACE . '/ ',
            $text
        );
        $text = str_replace('/' . Correction::NO_BREAK_SPACE, '/ ', $text);

        $node->setText($text);
    }
}
