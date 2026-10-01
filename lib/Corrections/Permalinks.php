<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Resolves permalinks to their readable counterpart.
 */
class Permalinks extends Correction
{
    /**
     * Resolve permalinks.
     *
     * This method is called when applying `$typographer->permalinks()`.
     */
    public function apply()
    {
        foreach ($this->inspector->select('a[href*="@"]') as $link) {
            if (preg_match(
                '/\/@\/(page|file)\/(.*)/',
                $link->getAttribute('href'),
                $matches
            )) {
                [, $model, $uuid] = $matches;

                if ($resolved = site()->find($model . '://' . $uuid)) {
                    $link->setAttribute('href', $resolved->url());
                }
            }
        }
    }
}
