<?php

namespace Hananils\Document\Nodes;

use Hananils\Document\Comparisons;

trait CanFilter
{
    protected bool $accepted = true;
    protected array $filters = [];

    /**
     * Checks if the nodes collection is filtered.
     */
    public function isFiltered(): bool
    {
        return count($this->filters) > 0;
    }

    /**
     * Stores a filter that will be used while processing the iterator.
     * The given method will be used to retrieve a value for comparison.
     *
     * @param $methodName The name of the method to retrieve the node value from.
     * @param $comparison The operator used for comparison.
     * @param $value The value the node value should be compared to.
     */
    public function filterBy(
        string $methodName,
        string $comparison = '==',
        mixed $value = ''
    ): self {
        if ($comparison = Comparisons::tryFrom($comparison)) {
            $this->filters[] = [$methodName, $comparison, $value];
        }

        return $this;
    }

    /**
     * Applies all filters when processing the iterator.
     */
    private function applyFilters(): void
    {
        $filters = $this->filters;

        while ($this->accepted === true && count($filters)) {
            [$filterName, $comparison, $value] = array_shift($filters);

            if (str_starts_with((string) $filterName, '@')) {
                $filterName = substr((string) $filterName, 1);
                $nodeValue = $this->getValueFromAttribute($filterName);
            } else {
                $nodeValue = $this->getValueFromMethod($filterName);
            }

            if ($nodeValue === null) {
                $this->accepted === false;
                continue;
            }

            $validator = 'validate' . $comparison->name;
            $this->accepted = $comparison->{$validator}($nodeValue, $value);
        }
    }

    /**
     * Retrieves the node value from the given method.
     *
     * @param $method The method name.
     */
    private function getValueFromMethod(string $method): mixed
    {
        if (method_exists($this->current(), $method)) {
            return $this->current()->{$method}();
        }

        return null;
    }

    /**
     * Retrieves the node value from the given attribute.
     *
     * @param $method The attribute name.
     */
    private function getValueFromAttribute(string $attribute): string
    {
        $element = $this->current();

        if ($element->isText()) {
            $element = $element->parent();
        }

        if (!$element) {
            return '';
        }

        return $element->getAttribute($attribute) ?? '';
    }
}
