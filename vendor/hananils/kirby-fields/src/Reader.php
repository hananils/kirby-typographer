<?php

namespace Hananils\Fields;

use Kirby\Content\Field;
use Kirby\Cms\App;
use Kirby\Cms\Language;
use Locale;

class Reader
{
    private App $kirby;
    private Field $field;

    public function __construct(Field $field)
    {
        $this->kirby = kirby();
        $this->field = $field;
    }

    public static function for(Field|null $field = null): self
    {
        return new self($field);
    }

    /**
     * Get the primary language code of the current locale by checking
     * – in that order – the multilingual settings, the configuration or the
     * server defaults.
     */
    public function locale(): string
    {
        // Check if we an in a multilingual context
        if ($language = $this->language()) {
            return $language->locale(LC_ALL) ??
                array_first($language->locale());
        }

        // Check if a locale if defined in the configuration
        if ($locale = option('locale', null)) {
            return $locale;
        }

        // Fall back to the server default
        return Locale::getPrimaryLanguage(Locale::getDefault());
    }

    /**
     * Returns the language object of the field content if the site is multilingual.
     * Return null for all single language installs.
     */
    public function language(): Language|null
    {
        $language = $this->kirby->defaultLanguage();

        if ($this->isTranslated()) {
            $language = $this->kirby->language();
        }

        return $language;
    }

    /**
     * Checks if the field content has been translated. This methods takes
     * into account whether a translation file actually contains diverging
     * content or just copies the source language.
     */
    public function isTranslated($code = null): bool
    {
        // Check if we are in a secondary language context and – if so – a
        // translation for the page has been created already.
        if (!$this->isSecondaryLanguage() || !$this->translationExists()) {
            return false;
        }

        // Check if source content exist
        $defaultLanguage = $this->kirby->defaultLanguage()->code();
        $source = $this->field
            ->model()
            ->content($defaultLanguage)
            ->get($this->field->key());

        if ($source->isEmpty()) {
            return false;
        }

        // Check if field translation differs from source.
        // Keep in mind that this doesn't make sure content is actually
        // translated, it's just our best educated guess.
        $translation = $this->field
            ->model()
            ->content($code)
            ->get($this->field->key());

        if ($source->value() === $translation->value()) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the field content is in a secondary language.
     */
    public function isSecondaryLanguage(): bool
    {
        if (
            !$this->kirby->multilang() ||
            $this->kirby->language()->isDefault()
        ) {
            return false;
        }

        return $this->definition('translate') ?? true;
    }

    /**
     * Checks if a translated version of the current page exists.
     */
    public function translationExists(string|null $code = null): bool
    {
        if (!$this->kirby->multilang()) {
            return false;
        }

        return $this->field->model()->translation($code)->exists();
    }

    /**
     * Returns the field definition, either in full or by key.
     */
    public function definition(string|null $key = null): mixed
    {
        // Get the field definition from the page blueprint
        $blueprint = $this->field->model()->blueprint();
        $definition = $blueprint->field($this->field->key());

        if ($key !== null) {
            return $definition[$key] ?? null;
        }

        return $definition;
    }

    /**
     * Returns the field type.
     */
    public function type(): string|null
    {
        return $this->definition('type');
    }

    /**
     * Formats the field value, returning HTML where possible.
     * Defaults to the raw field value.
     */
    public function format(string|null $format = null): string
    {
        if ($this->field === null) {
            return '';
        }

        // Get formatter from field
        if ($format === null) {
            $format = match ($this->type()) {
                'textarea' => 'block',
                'text' => 'inline',
                'blocks' => 'html',
                default => 'value'
            };
        }

        // Get formatted content
        return match ($format) {
            'block' => $this->field->kirbytext(),
            'inline' => $this->field->kirbytextinline(),
            'html' => $this->field->toHtml(),
            default => $this->field->value
        };
    }
}
