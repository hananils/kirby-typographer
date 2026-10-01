<?php

use Hananils\Fields\Reader;

return [
    /**
     * Converts the field content to HTML and applies typographic
     * corrections. If no format is specified, it is determined based
     * on the field type defined in the blueprint.
     *
     * For performance reasons, you should always define a format.
     *
     * This method reads the content locale from the site or page context:
     * For a single language site, the `locale` option from the config is
     * used. For a multi language site, the locale is determined based on
     * the content language. If a translation contains a copy of the source
     * language, it is considered as untranslated.
     *
     * @param $format The content format, either `block`, `inline`, `html` or `value`.
     * @param $options Array of options to be passed to Typographer.
     */
    'typographer' => function (
        $field,
        ?string $format = null,
        array $options = []
    ) {
        $reader = Reader::for($field);

        $typographer = typographer(
            $reader->format($format),
            $options,
            $reader->locale()
        );
        $typographer->field = $field;

        return $typographer;
    }
];
