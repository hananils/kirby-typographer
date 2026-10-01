<?php

namespace Hananils\Plugins;

use ArrayIterator;
use FilesystemIterator;
use FilterIterator;

/**
 * Discovery iterates over PHP, YAML and JSON files in a given directory.
 * It will not fail on non-existing paths and will fall back to an empty iterator.
 */
class Discovery extends FilterIterator
{
    public function __construct($path)
    {
        $realpath = realpath($path);

        // Check if directory exists
        if ($realpath === false || is_dir($realpath) === false) {
            // Create empty iterator for non-existent directories
            $iterator = new ArrayIterator();
        } else {
            // Create filesytem iterator
            $iterator = new FilesystemIterator($path);
        }

        parent::__construct($iterator);
    }

    /**
     * Filters the files in the directory.
     */
    public function accept(): bool
    {
        $file = $this->current();

        if ($file->isDir()) {
            return true;
        }

        if (in_array($file->getExtension(), ['php', 'yml', 'json'])) {
            return true;
        }

        return false;
    }
}
