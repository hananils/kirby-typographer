<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Resolves UUIDs in links to their readable counterpart.
 */
class Uuid extends Correction
{
    /**
     * Resolve UUIDs.
     *
     * This method is called when applying `$typographer->uuid()`.
     */
    public function apply()
    {
        foreach (
            $this->inspector->select(
                '//a[starts-with(@href, "page://") or starts-with(@href, "file://")]',
                xpath: true
            )
            as $link
        ) {
            if ($resolved = site()->find($link->attribute('href'))) {
                $link->attribute('href', $resolved->url());
            }
        }
    }
}
