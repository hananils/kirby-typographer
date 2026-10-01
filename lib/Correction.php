<?php

namespace Hananils\Typographer;

use Hananils\Document\Inspector;
use Hananils\Document\Nodes;
use Kirby\Exception\BadMethodCallException;

/**
 * Base class used by all corrections.
 */
abstract class Correction
{
    public const NO_BREAK_SPACE = '&#160;';
    public const NARROW_NO_BREAK_SPACE = '&#8239;';
    public const NON_BREAKING_HYPHEN = '&#8209;';

    public Inspector $inspector;
    public Options $options;
    public Locale $locale;

    /**
     * Initialises the correction.
     *
     * @param $inspector The inspector holding the document to be corrected.
     * @param $options The correction options.
     * @param $locale The current locale.
     */
    public function __construct(
        Inspector $inspector,
        Locale $locale,
        Options $options
    ) {
        $this->inspector = $inspector;
        $this->locale = $locale;
        $this->options = $options;
    }

    public function __call(string $name, array $arguments = [])
    {
        if (method_exists($this->inspector, $name)) {
            return $this->inspector->{$name}(...$arguments);
        }

        // Nothing found
        throw new BadMethodCallException(
            "Call to unknown method $name on Correction."
        );
    }

    /**
     * Applies typographic corrections.
     */
    abstract public function apply();

    /**
     * Converts current document to a texts iterator.
     */
    public function toTexts(): Nodes
    {
        return $this->inspector->toTexts();
    }

    /**
     * Converts current document to a nodes iterator.
     */
    public function toNodes(): Nodes
    {
        return $this->inspector->toNodes();
    }
}
