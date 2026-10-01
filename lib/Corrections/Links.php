<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;
use Kirby\Http\Url;

/**
 * Links unifies link display, by removing protocol and `www` subdomains from
 * the readable output, adding the base url to all relative links in the `href`
 * attribute.
 */
class Links extends Correction
{
    /**
     * Formats links.
     *
     * This method is called when applying `$typographer->links()`.
     */
    public function apply()
    {
        $this->inspector
            ->select('a')
            ->toSelection()
            ->each(function ($link) {
                $text= $link->text();

                if (str_starts_with($text, 'http') || str_starts_with($text, 'www')) {
                    $link->text(Url::short($text));
                }

                $href = $link->getAttribute('href');
                if ($href && str_starts_with($href, '/')) {
                    $link->setAttribute('href', kirby()->url() . $href);
                }
            });
    }
}
