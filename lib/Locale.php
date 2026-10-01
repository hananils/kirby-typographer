<?php

namespace Hananils\Typographer;

use DirectoryIterator;
use Hananils\Document\Node;
use Hananils\Typographer\Enums\QuoteStyle;
use Stringable;

/**
 * Locale manager providing typographic settings for the different locales,
 * e. g. single or double quotes, apostrophes or dashes.
 */
class Locale implements Stringable
{
    private static array $locales = [];
    private static array $data = [];
    private static array $defaults = [
        'quotes' => [
            'default' => [
                'double' => ['opening' => '“', 'closing' => '”'],
                'single' => ['opening' => '‘', 'closing' => '’']
            ]
        ]
    ];

    private string $locale = 'en';

    /**
     * Locale constructor.
     *
     * @param $locale The locale.
     */
    public function __construct(string $locale = 'en')
    {
        $this->discover();
        $this->set($locale);
    }

    /**
     * Creates a new Locale instance from a node, checking for the closest
     * locale definition in the ancestors. Returns `null` if no locale is defined
     * in the tree.
     *
     * @param Node $node The node.
     */
    public static function from(Node $node, $locale = 'en'): ?self
    {
        $withLang = $node->parents()->filterBy('@lang', '!=', '');

        if ($withLang->isNotEmpty()) {
            $lang = $withLang->first()->getAttribute('lang');
            $locale = str_replace('-', '_', $lang);
        }

        return new Locale($locale);
    }

    /**
     * Finds all locales provided inside the locales directory.
     */
    private function discover(): void
    {
        // Check if discovery already run
        if (self::$locales !== []) {
            return;
        }

        $directory = new DirectoryIterator(__DIR__ . '/Locales');

        foreach ($directory as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            // Get locale name
            $locale = $file->getBasename('.json');

            // Store path for locale
            self::$locales[$locale] = $file->getPathname();
        }
    }

    /**
     * Sets the locale by looking up the best match for the given identifier.
     *
     * @param $locale The locale string.
     */
    public function set(string $locale = 'en'): void
    {
        // Setting the third parameter to `false` makes sure the returned locale
        // follows the notation in the directory, keeping case and subtag separator.
        // This is important because PHP uses underscores as separators, while
        // HTML requires dashes.
        $this->locale = locale_lookup(
            array_keys(self::$locales),
            $locale,
            false,
            'en'
        );
        $this->load($this->locale);
    }

    /**
     * Loads the data associated with the current locale.
     *
     * @param $locale The locale string.
     */
    private function load(string $locale = 'en'): void
    {
        if (isset(self::$data[$locale])) {
            return;
        }

        $data = [];
        $file = self::$locales[$locale] ?? null;

        if ($file && file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
        }

        self::$data[$locale] = array_merge(self::$defaults, $data);
    }

    /**
     * Returns the current locale.
     */
    public function get(): string
    {
        return $this->locale;
    }

    /**
     * Returns the locale typographic settings, either all or just the
     * specified key.
     *
     * @param $key The settings key.
     */
    public function data($key = null): mixed
    {
        $data = self::$data[$this->locale];

        if ($key) {
            if (isset($data[$key])) {
                return $data[$key];
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Returns the quote style of the current locale.
     *
     * @param $style The name of the quote style, either `default` or `alternatives`.
     */
    public function quotes(QuoteStyle $style = QuoteStyle::Default): array
    {
        $data = $this->data('quotes');

        return $data[$style->value] ?? null;
    }

    /**
     * Matches the current locale with the given locale identifier.
     */
    public function matches(string $locale): bool
    {
        return $this->locale === $locale;
    }

    /**
     * Returns the current locale string.
     */
    public function __toString(): string
    {
        return $this->locale;
    }
}
