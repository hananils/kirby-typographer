<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Plugins\PluginManager;
use Hananils\Typographer\Corrections;
use Hananils\Typographer\Typographer;

Corrections::addDirectory(kirby()->root('site') . '/corrections');

/**
 * Helper to apply typographic corrections to an HTML document or fragment.
 *
 * @param $html The HTML.
 * @param $options The options.
 * @param $locale The locale.
 */
function typographer(
    string $html,
    array $options = [],
    null|string $locale = null
): Typographer {
    $kirby = kirby();

    // Get global options
    $defaults = option('hananils.typographer', []);
    unset($defaults['defaults']);
    if (!empty($defaults) && is_array($defaults)) {
        $options = array_merge($defaults, $options);
    }

    // Get locale
    if ($locale === null && $kirby->multilang()) {
        $locale =
            $kirby->language()->locale(LC_ALL) ??
            array_first($kirby->language()->locale());
    }

    if ($locale === null) {
        $locale = option('locale', 'en');
    }

    // Create Typographer
    return Typographer::fromString($html, $locale, $options);
}

PluginManager::autoload('typographer', __DIR__);
