<?php

use Hananils\Document\Elements;
use Hananils\Fields\Reader;

return [
    /**
     * Applies typographic rules to the blocks.
     *
     * This method reads the content locale from the site or page context:
     * For a single language site, the `locale` option from the config is
     * used. For a multi language site, the locale is determined based on
     * the content language. If a translation contains a copy of the source
     * language, it is considered as untranslated.
     *
     * @param $options Array of options to be passed to Typographer.
     */
    'typographer' => function (?string $format = null, array $options = []) {
        $html = $this->toHtml();

        if ($format === 'inline') {
            $html = strip_tags(
                (string) $html,
                Elements::byFormattingContext('inline')
            );
        }

        $typographer = typographer(
            $html,
            $options,
            Reader::for($this->field())->locale()
        );
        $typographer->field = $this->field();

        return $typographer;
    }
];
