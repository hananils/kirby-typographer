<?php

namespace Hananils\Typographer;

class Options
{
    private array $options = [];

    /**
     * @param $options The options.
     */
    public function __construct(array $options = [])
    {
        $this->setAll($options);
    }

    /**
     * Sets options of a single correction type.
     *
     * @param $id The correction id.
     * @param $options The correction options.
     */
    public function set(string $id, mixed $options = []): self
    {
        $this->options[$id] = $options;

        return $this;
    }

    /**
     * Sets all options provided as an array mapping ids to options.
     *
     * @param $options The options.
     */
    public function setAll(array $options): self
    {
        foreach ($options as $id => $option) {
            $this->set($id, $option);
        }

        return $this;
    }

    /**
     * Returns an options by id.
     *
     * @param $id The correction id.
     */
    public function get(string $id = '', mixed $default = []): mixed
    {
        return $this->options[$id] ?? $default;
    }

    /**
     * Returns all options.
     */
    public function getAll(): array
    {
        return $this->options;
    }

    /**
     * Unsets an option by id.
     *
     * @param $id The option id.
     */
    public function unset(string $id): self
    {
        if ($this->has($id)) {
            unset($this->options[$id]);
        }

        return $this;
    }

    /**
     * Resets all options.
     */
    public function reset(): self
    {
        $this->options = [];

        return $this;
    }

    /**
     * Checks if the correction has options.
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->options);
    }

    /**
     * Checks if the options are empty.
     */
    public function isEmpty(): bool
    {
        return $this->options === [];
    }

    /**
     * Checks if the options are not empty.
     */
    public function isNotEmpty(): bool
    {
        return $this->options !== [];
    }
}
