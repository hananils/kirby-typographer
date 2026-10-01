<?php

namespace Hananils\Document\Node;

use ArrayIterator;
use Dom\TokenList;

trait HasAttributes
{
    /**
     * Checks if the current node at the given attribute set.
     *
     * @param $name The attribute name.
     */
    public function hasAttribute(string $name): bool
    {
        if ($this->isText()) {
            return false;
        }

        return $this->item->hasAttribute($name);
    }

    /**
     * Checks if the given nodes has any attribute.
     */
    public function hasAttributes(): bool
    {
        if ($this->isText()) {
            return false;
        }

        return $this->item->attributes->count() > 0;
    }

    /**
     * Gets an attribute by name.
     *
     * @param $name The attribute name.
     */
    public function getAttribute(string $name): string|null
    {
        if ($this->isText()) {
            return null;
        }

        return $this->item->getAttribute($name);
    }

    /**
     * Sets an attribute by name.
     *
     * @param $name The attribute name.
     * @param $value The new attribute value.
     */
    public function setAttribute(string $name, string $value): self
    {
        if ($this->isElement()) {
            $this->item->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Convenience method that either sets or gets an attribute.
     *
     * @param $name The attribute name.
     * @param $value The new attribute value, if used as setter.
     */
    public function attribute(
        string $name,
        null|string $value = null
    ): self|string|null {
        if ($value !== null) {
            return $this->setAttribute($name, $value);
        }

        return $this->getAttribute($name);
    }

    /**
     * Gets all node attributes.
     */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->item->attributes as $attribute) {
            $attributes[$attribute->name] = $attribute->value;
        }

        return $attributes;
    }

    /**
     * Sets attributes.
     *
     * @param $attributes The new attributes.
     */
    public function setAttributes(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Convenience methods that either sets or gets attributes.
     *
     * @param $attributes The new attributes, if used as setter.
     */
    public function attributes(array $attributes = []): self|array
    {
        if (!empty($attributes)) {
            return $this->setAttributes($attributes);
        }

        return $this->getAttributes();
    }

    /**
     * Removes an attribute by name.
     *
     * @param $name The attribute name.
     */
    public function removeAttribute(string $name): self
    {
        $this->item->removeAttribute($name);

        return $this;
    }

    /**
     * Removes attributes by names.
     *
     * @param $names The attribute names.
     */
    public function removeAttributes(array $names): self
    {
        foreach ($names as $name) {
            $this->removeAttribute($name);
        }

        return $this;
    }

    /**
     * Gets the element id.
     */
    public function getId(): string
    {
        return $this->item->id;
    }

    /**
     * Sets the element id.
     *
     * @param $id The new id.
     */
    public function setId(string $id): self
    {
        $this->item->id = $id;

        return $this;
    }

    /**
     * Convenience method that either sets or gets the element id.
     *
     * @param $id The new id, if used as setter.
     */
    public function id(string $id = ''): string|self
    {
        if ($id) {
            return $this->setId($id);
        }

        return $this->getId();
    }

    /**
     * Gets all class names of the element.
     */
    public function getClasses(): TokenList
    {
        return $this->item->classList;
    }

    /**
     * Sets all given class names, overriding existing ones.
     *
     * @param $classes The new class names.
     */
    public function setClasses(array $classes): self
    {
        $this->setAttribute('class', implode(' ', $classes));

        return $this;
    }

    /**
     * Convenience method that either sets or gets class names.
     *
     * @param $classes The new class names, if used as setter.
     */
    public function classes(array $classes = []): self|array
    {
        if (!empty($classes)) {
            return $this->setClasses($classes);
        }

        return $this->getClasses();
    }

    /**
     * Toggles class name.
     *
     * @param $name The class name.
     */
    public function toggleClass(string $name, ?bool $force = null): self
    {
        $this->item->classList->toggle($name, $force);

        return $this;
    }

    /**
     * Removes class name.
     *
     * @param $name The class name.
     */
    public function removeClass(string $name): self
    {
        $this->item->classList->remove($name);

        return $this;
    }

    /**
     * Replaces a class name.
     *
     * @param $old The old class name.
     * @param $new The new class name.
     */
    public function replaceClass(string $old, string $new): self
    {
        $this->item->classList->replace($old, $new);

        return $this;
    }

    /**
     * Adds class name, keeping all existing ones.
     *
     * @param $name The class name.
     */
    public function addClass(string $name): self
    {
        $this->item->classList->add($name);

        return $this;
    }

    /**
     * Checks if the current node has the current class name set.
     *
     * @param $name The class name.
     */
    public function hasClass(string $name): bool
    {
        return $this->item->classList->contains($name);
    }

    /**
     * Gets a data attribute by name.
     *
     * @param $name The attribute name.
     */
    public function getData($name): string
    {
        return $this->getAttribute('data-' . $name);
    }

    /**
     * Sets a data attribute by name.
     *
     * @param $name The attribute name.
     * @param $value The attribute value.
     */
    public function setData($name, $value): self
    {
        $this->setAttribute('data-' . $name, $value);

        return $this;
    }

    /**
     * Gets the current node's dataset.
     */
    public function getDataset(): array
    {
        $dataset = [];

        foreach ($this->getAttributes() as $name => $value) {
            if (str_starts_with((string) $name, 'data-')) {
                $dataset[substr((string) $name, 5)] = $value;
            }
        }

        return $dataset;
    }

    /**
     * Sets the current node's dataset, with the keys representing the data
     * attribute without `data` prefix, and the values representing the
     * attribute values.
     *
     * @param $dataset The dataset.
     */
    public function setDataset(array $dataset): self
    {
        foreach ($dataset as $name => $value) {
            $this->setData($name, $value);
        }

        return $this;
    }

    /**
     * Convenience method that either sets or gets the dataset.
     *
     * @param array $dataset The dataset, if used as setter.
     */
    public function dataset(array $dataset = []): self|array
    {
        if (!empty($dataset)) {
            return $this->setDataset($dataset);
        }

        return $this->getDataset();
    }
}
