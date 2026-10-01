<?php

namespace Hananils\Typographer;

use Closure;
use DirectoryIterator;
use Exception;
use IntlChar;

/**
 * Corrections represent typographic rule sets that can be applied to documents
 * using Typographer via instructions.
 */
class Corrections
{
    /**
     * List of directories holding corrections.
     */
    public static array $directories = [];

    /**
     * Adds a directory to the list of diretories holding corrections.
     *
     * @param $path The path.
     */
    public static function addDirectory(string $path): void
    {
        if ($path = realpath($path)) {
            if (!str_starts_with($path, kirby()->root('site'))) {
                throw new Exception('Directory path outside of site root.');
            }

            self::$directories[] = $path;
        }
    }

    /**
     * Reads correction directory, creates correction ids and stores class name as reference.
     */
    private static function discover(): void
    {
        // Check if discovery already run
        if (self::$corrections !== []) {
            return;
        }

        // Discover corrections
        foreach (self::$directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $files = new DirectoryIterator($directory);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $basename = $file->getBasename('.php');

                // Remove default prefix from corrections, e. g. DefaultCorrection
                // for classes and default.correction.php for closures.
                $name = preg_replace('/^(Default|default\.)/', '', $basename);

                // Create id
                $id = str_replace(['-', '_'], ' ', $name);
                $id = ucwords($id);
                $id = str_replace(' ', '', $id);
                $id = lcfirst($name);

                // Get correction
                if (IntlChar::isupper($basename[0])) {
                    $correction = '\Hananils\Typographer\Corrections\\' . $name;
                } else {
                    $correction = include $file->getPathname();
                }

                // Set correction
                self::set($id, $correction, $basename !== $name);
            }
        }
    }

    /**
     * Currated list of default corrections in their processing order.
     * Optional corrections are called manually which determines their
     * order individually. They should not be added to this list.
     */
    public static array $processingOrder = [
        'abbreviations',
        'dashes',
        'middot',
        'trademarks',
        'ellipsis',
        'caps',
        'primes',
        'quotes',
        'apostrophes',
        'links',
        'lists',
        'math',
        'slashes',
        // 'shortParagraphs',
        'permalinks',
        'uuid'
    ];

    /**
     * Adds a correction to the default processing instructions.
     *
     * @param $id The correction id.
     */
    public static function setDefault(string $id): void
    {
        // Remove existing default so that the new default is added at the end
        // of the processing instructions.
        self::unsetDefault($id);
        self::$processingOrder[] = $id;
    }

    /**
     * Removes a correction form the default processing instructions.
     *
     * @param $id The correction id.
     */
    public static function unsetDefault($id): void
    {
        $index = array_search($id, self::$processingOrder);

        unset(self::$processingOrder[$index]);
    }

    /**
     * Sets the default processing order.
     *
     * @param $ids Array of correction ids in processing order.
     */
    public static function setDefaults(array $ids): void
    {
        self::$processingOrder = $ids;
    }

    /**
     * Resets the default processing order.
     */
    public static function resetDefaults(): void
    {
        self::$processingOrder = [];
    }

    /**
     * List of all available corrections.
     */
    public static array $corrections = [];

    /**
     * Stores correction under given id.
     *
     * @param $id The correction id.
     * @param $correction The correction, either as namespace or closure.
     */
    public static function set(
        string $id,
        string|Closure $correction,
        bool $asDefault = false
    ) {
        if (is_string($correction) && !class_exists($correction)) {
            throw new Exception(
                'Correction class ' . $correction . ' does not exist.'
            );
        }

        self::$corrections[$id] = $correction;

        // Append custom default to the processing order
        if ($asDefault) {
            self::$processingOrder[] = $id;
        }
    }

    /**
     * Finds correction by given id.
     *
     * @param $id The correction id.
     */
    public static function get(string $id = ''): string|Closure|array
    {
        self::discover();

        if (self::has($id)) {
            return self::$corrections[$id];
        }

        return self::$corrections;
    }

    /**
     * Checks if correction id exists.
     *
     * @param $id The correction id.
     */
    public static function has(string $id): bool
    {
        self::discover();

        return array_key_exists($id, self::$corrections);
    }
}
