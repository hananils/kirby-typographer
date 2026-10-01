<?php

namespace Hananils\Typographer;

use BadMethodCallException;
use Closure;
use Dom\HTMLDocument;
use Hananils\Document\Inspector;
use Kirby\Content\Field;

/**
 * Main Typographer class handling corrections and instructions.
 *
 * @copyright hana+nils · Büro für Gestaltung
 * @author Nils Hörrmann <nils.hoerrmann@hananils.de>
 * @link https://hananils.de
 * @license https://kirby.hananils.de/licenses/plus
 */
class Typographer implements \Stringable
{
    public Inspector $inspector;
    public Locale $locale;
    public Options $options;

    private array $instructions = [];
    public bool $processed = false;
    public ?Field $field = null;

    /**
     * @param $inspector The document inspector.
     * @param $locale The locale.
     * @param $options The default options.
     */
    public function __construct(
        Inspector $inspector,
        string $locale = 'en',
        array $options = []
    ) {
        Corrections::addDirectory(__DIR__ . '/Corrections');

        $this->inspector = $inspector;
        $this->locale = new Locale($locale);
        $this->options = new Options($options);
    }

    /**
     * Creates a new Typographer instance from an HTML string.
     *
     * @param $html The HTML string.
     * @param $locale The locale.
     * @param $options The global options.
     */
    public static function fromString(
        string $html,
        string $locale = 'en',
        array $options = []
    ): self {
        $inspector = Inspector::fromString($html);

        return new Typographer($inspector, $locale, $options);
    }

    /**
     * Creates a new Typographer instance from an existing HTMLDocument.
     *
     * @param $document The HTMLDocument object.
     * @param $fragment Flag whether the content should be treated as fragement, defaults to true.
     * @param $locale The locale.
     * @param $options The global options.
     */
    public static function fromDocument(
        HTMLDocument $document,
        bool $fragment = true,
        string $locale = 'en',
        array $options = []
    ): self {
        $inspector = Inspector::fromDocument($document, $fragment);

        return new Typographer($inspector, $locale, $options);
    }

    /**
     * Provides shortcuts to setter getter methods, typographic instructions or
     * field methods (in case a field is set of the class).
     */
    public function __call(string $id, array $arguments = [])
    {
        // Check if this can be mapped to a getter or setter method
        $prefix = $arguments === [] ? 'get' : 'set';
        $name = $prefix . ucfirst($id);
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        // Check if this is an instruction for a correction
        if (Corrections::has($id)) {
            $options = $arguments[0] ?? [];

            // Remove instruction
            if ($options === false) {
                // We can only remove instructions if any were set at all.
                // Otherwise the removed instruction will be added back again
                // later on when typography is applied.
                if ($this->instructions === []) {
                    $this->setDefaults();
                }

                return $this->unset($id);
            }

            // Set instruction
            return $this->set($id, $options);
        }

        // If a Kirby field has been registered, forward call to field and
        // return the result
        if ($this->field !== null) {
            return $this->toField()->{$id}();
        }

        // Nothing found
        throw new BadMethodCallException(
            "Call to unknown method $id on Typographer."
        );
    }

    /**
     * Stores processing instructions for a correction. By default, if an
     * instruction for the given id already exists, it is replaced by the new
     * one, moving its id to the end of the list of instructions and thus
     * changing the execution order. This can be circumvented by setting the
     * unique flag to `false`
     *
     * @param $id The instruction id.
     * @param $options The instruction options.
     * @param $unique Flag for unique instruction handling.
     */
    public function set(string $id, array $options = [], $unique = true): self
    {
        if (Corrections::has($id)) {
            $this->processed = false;

            // Merge with existing instructions
            if ($unique === true && $this->has($id) !== false) {
                $options = $this->get($id)->setAll($options);

                $this->unset($id);
            } else {
                $options = new Options($options);
            }

            $this->instructions[] = [$id, $options];
        }

        return $this;
    }

    /**
     * Returns the instructions for a given correction.
     *
     * @param $id The correction id.
     */
    public function get(string $id): Options|false
    {
        if ($index = $this->for($id)) {
            return $this->instructions[$index][1];
        }

        return false;
    }

    /**
     * Removes a correction from the processing instructions.
     *
     * @param $id The instruction id.
     */
    public function unset(string $id): self
    {
        if ($index = $this->for($id)) {
            unset($this->instructions[$index]);
        }

        return $this;
    }

    /**
     * Resets the list of instructions.
     */
    public function reset(): self
    {
        $this->processed = false;
        $this->instructions = [];

        return $this;
    }

    /**
     * Finds and returns the index of instructions for a correction.
     *
     * @param $id The correction id.
     */
    private function for(string $id): int|false
    {
        if ($this->instructions === []) {
            return false;
        }

        return array_search($id, array_column($this->instructions, 0));
    }

    public function has(string $id): bool
    {
        return $this->for($id) !== false;
    }

    /**
     * Sets the default instructions.
     *
     * @param $options Associative array of options with the correction id as key.
     */
    public function setDefaults(array $options = []): self
    {
        $options = array_merge($this->options->getAll(), $options);

        foreach (Corrections::$processingOrder as $instruction) {
            $this->set(
                $instruction,
                $options[$instruction] ?? [],
                unique: false
            );
        }

        return $this;
    }

    /**
     * Reads instructions and applies typographic corrections in defined order.
     */
    public function apply(): self
    {
        if ($this->processed === true || $this->inspector->isEmpty()) {
            return $this;
        }

        if ($this->instructions === []) {
            $this->setDefaults();
        }

        foreach ($this->instructions as $instruction) {
            [$id, $options] = $instruction;

            if ($correction = Corrections::get($id)) {
                if (is_string($correction)) {
                    $this->applyByClass($correction, $options);
                } else {
                    $this->applyByClosure($correction, $options);
                }
            }
        }

        $this->processed = true;

        return $this;
    }

    /**
     * Applies correction by class name.
     *
     * @param $correction The class name.
     * @param $options The correction options.
     */
    private function applyByClass(string $correction, Options $options): void
    {
        $method = new $correction($this->inspector, $this->locale, $options);
        $method->apply();

        $this->inspector = $method->inspector->clearSelection();
    }

    /**
     * Applies correction by closure.
     *
     * @param $correction The closure.
     * @param $options The correction options.
     */
    private function applyByClosure(Closure $correction, Options $options): void
    {
        $inspector = $correction($this->inspector, $this->locale, $options);

        if ($inspector instanceof Inspector) {
            $this->inspector = $inspector->clearSelection();
        }
    }

    /**
     * If Designer is installed, returns a Designer instance with the current
     * document for further DOM manipulation.
     *
     * If Designer is not installed, returns the current Typographer instance
     * without any changes.
     */
    public function designer()
    {
        if (class_exists('\Hananils\Designer')) {
            $this->apply();

            $inspector = $this->inspector;
            if ($inspector->isFiltered()) {
                $document = $inspector->reduce()->document;
            } else {
                $document = $inspector->clearSelection()->document;
            }

            $designer = \Hananils\Designer::fromDocument(
                $document,
                $inspector->fragment
            );
            $designer->field = $this->field;

            return $designer;
        }

        return $this;
    }

    /**
     * Returns the source field – if Typographer has a field reference –
     * applying all typographic changes first.
     *
     * Field and block methods will set the source field, the `typographer()`
     * helper will have no reference by default.
     */
    public function toField(): ?Field
    {
        if ($this->field !== null) {
            $this->field->value = $this->toHtml();
        }

        return $this->field;
    }

    /**
     * Applies all corrections and returns the resulting HTML as string.
     *
     * @param $collapse Toggles whitespace collapsing.
     * @param $trim Toggles content trimming.
     */
    public function toHtml(bool $collapse = false, bool $trim = false): string
    {
        $this->apply();

        return $this->inspector->toHtml($collapse, $trim);
    }

    /**
     * Applies all corrections and returns a text only respresentation with
     * all tags removed.
     */
    public function toPlainText()
    {
        $this->apply();

        return strip_tags((string) $this->inspector->toHtml(true, true));
    }

    /**
     * Returns a string representation of the current document.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
