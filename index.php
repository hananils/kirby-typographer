<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

function typographer($html, $flow = 'block', $locale = null)
{
    if ($locale === null) {
        if (kirby()->language()) {
            $locale = kirby()
                ->language()
                ->locale(LC_ALL);
        } elseif (option('locale')) {
            $locale = option('locale');
        }
    }

    $typographer = new Typographer($locale, $flow);

    // Set global corrections
    if ($corrections = option('hananils.typographer.corrections', null)) {
        if (is_array($corrections)) {
            $typographer->corrections($corrections);
        }
    }

    // Set global options
    if ($options = option('hananils.typographer.options', null)) {
        if (is_array($options)) {
            $typographer->options($options);
        }
    }

    return $typographer->parse($html);
}

Kirby::plugin('hananils/typographer', [
    'fieldMethods' => [
        'typographer' => function ($field, $flow = null, $convert = true) {
            $html = '';

            if ($convert) {
                if ($flow === 'block' || $flow === 'inline') {
                    // The flow type has explicitly been set to inline or block
                    $html = $field->toFlow($flow);
                } else {
                    // Get field definition
                    $definition = $field->toDefinition();
                    $type = $definition['type'];

                    // Set default flow
                    $flow = 'block';

                    // Automatically convert field value to HTML by field type
                    if ($type === 'blocks') {
                        // Blocks are expected to generate block flow
                        $html = $field->toHtml();
                    } elseif ($type === 'textarea') {
                        // Textareas are expected to generate block flow
                        $html = $field->toFlow('block');
                    } elseif ($type === 'writer') {
                        // Writer fields store HTML directly
                        $html = $field->value;

                        // Check if the field has inline flow
                        if ($definition['inline'] === true) {
                            $flow = 'inline';
                        }
                    } elseif ($type === 'list') {
                        // List fields store HTML directly
                        $html = $field->value;
                    } else {
                        // Treat other fields as inline flow
                        $html = $field->toFlow('inline');
                        $flow = 'inline';
                    }
                }
            } else {
                // Without conversion, the field value is expected to be valid
                // HTML. The Typographer class will treat it as block flow.
                $html = $field->value;
            }

            return typographer($html, $flow);
        },
        'toDefinition' => function ($field) {
            // Get the field definition from the page blueprint
            $blueprint = $field->parent()->blueprint();
            $definition = $blueprint->field($field->key());

            return $definition;
        },
        'toFlow' => function ($field, $flow = 'block') {
            $kirby = kirby();
            $text = $field->value;
            $data = [
                'parent' => $field->parent(),
                'field' => $field
            ];

            // Convert field value to HTML
            $text = $kirby->apply('kirbytext:before', compact('text'), 'text');
            $text = $kirby->markdown($text, [
                'inline' => $flow === 'inline'
            ]);
            $text = $kirby->kirbytags($text, $data);
            $text = $kirby->apply('kirbytext:after', compact('text'), 'text');

            return $text;
        }
    ],
    'blocksMethods' => [
        'typographer' => function () {
            // Blocks are expected to generate block flow
            $html = $this->toHtml();

            return typographer($html, 'block');
        }
    ],
    'blockMethods' => [
        'typographer' => function () {
            // Blocks are expected to generate block flow
            $html = $this->toHtml();

            return typographer($html, 'block');
        }
    ]
]);
