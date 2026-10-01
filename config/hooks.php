<?php

use Hananils\Typographer\Corrections;

return [
    'system.loadPlugins:after' => function (): void {
        $defaults = option('hananils.typographer.defaults', []);

        if (!empty($defaults) && is_array($defaults)) {
            Corrections::setDefaults($defaults);
        }
    }
];
