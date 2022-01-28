<?php

namespace Hananils\Corrections;

use Hananils\Correction;

class Widont extends Correction
{
    public $flow = 'block';

    public function apply()
    {
        $text = $this->xpath->query('//text()[contains(., " ")]');
        $last = $text->item($text->length - 1);

        if ($last) {
            $updated = $last->textContent;

            // Trailing whitespace
            if (substr($updated, -1) === ' ') {
                $updated = $updated . '&nbsp;';
            } else {
                $updated = $this->widont($updated);
            }

            // Make sure slashes break correctly
            $updated = str_replace(' /&nbsp;', '&nbsp;/ ', $updated);
            $updated = str_replace('/&nbsp;', '/ ', $updated);

            $last->textContent = html_entity_decode($updated);
        }
    }

    private function widont($string = null)
    {
        // Make sure $string is string
        $string ??= '';

        // Replace space between last word and punctuation
        $string = preg_replace_callback(
            '|(\S)\s(\S?)$|u',
            function ($matches) {
                return $matches[1] . '&nbsp;' . $matches[2];
            },
            $string
        );

        // Replace space between last two words
        return preg_replace_callback(
            '|(\s)(?=\S*$)(\S+)|u',
            function ($matches) {
                if (stripos($matches[2], '-')) {
                    $matches[2] = str_replace('-', '&#8209;', $matches[2]);
                }
                return '&nbsp;' . $matches[2];
            },
            $string
        );
    }
}
