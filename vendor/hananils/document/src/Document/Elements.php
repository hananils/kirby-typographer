<?php

namespace Hananils\Document;

use Dom\Element;
use Dom\Node;

/**
 * Element information is derived from the HTML Spec:
 * https://html.spec.whatwg.org/multipage/indices.html#row-content-categories
 */

class Elements
{
    private static array $cache = [];

    /**
     * Element content categories
     */
    private static array $categories = [
        'embedded',
        'flow',
        'form-associated',
        'heading',
        'interactive',
        'labelable',
        'listed',
        'metadata',
        'palpable',
        'phrasing',
        'resettable',
        'script-supporting',
        'sectioning',
        'submittable',
        'text',
        'transparent',

        /**
         * This category is non-standard and depicts elements which have preformatted
         * content that should not be altered by typographic parsers. Think of an
         * inline `script` element which would be rendered non-functional when changing
         * straight quotes into typographic ones.
         */
        'preformatted'
    ];

    /**
     * Element content context by name
     */
    private static array $elements = [
        'a' => [
            'categories' => ['flow', 'phrasing', 'interactive', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['transparent']
        ],
        'abbr' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'address' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'area' => [
            'categories' => ['flow', 'phrasing'],
            'parents' => ['phrasing'],
            'children' => []
        ],
        'article' => [
            'categories' => ['flow', 'sectioning', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'aside' => [
            'categories' => ['flow', 'sectioning', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'audio' => [
            'categories' => [
                'flow',
                'phrasing',
                'embedded',
                'interactive',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => ['source', 'track', 'transparent']
        ],
        'b' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'base' => [
            'categories' => ['metadata'],
            'parents' => ['head'],
            'children' => []
        ],
        'bdi' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'bdo' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'blockquote' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'body' => [
            'categories' => [],
            'parents' => ['html'],
            'children' => ['flow']
        ],
        'br' => [
            'categories' => ['flow', 'phrasing'],
            'parents' => ['phrasing'],
            'children' => []
        ],
        'button' => [
            'categories' => [
                'flow',
                'phrasing',
                'interactive',
                'listed',
                'labelable',
                'submittable',
                'form-associated',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'canvas' => [
            'categories' => ['flow', 'phrasing', 'embedded', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['transparent']
        ],
        'caption' => [
            'categories' => [],
            'parents' => ['table'],
            'children' => ['flow']
        ],
        'cite' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'code' => [
            'categories' => ['flow', 'phrasing', 'palpable', 'preformatted'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'col' => [
            'categories' => [],
            'parents' => ['colgroup'],
            'children' => []
        ],
        'colgroup' => [
            'categories' => [],
            'parents' => ['table'],
            'children' => ['col', 'template']
        ],
        'data' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'datalist' => [
            'categories' => ['flow', 'phrasing'],
            'parents' => ['phrasing'],
            'children' => ['phrasing', 'option', 'script-supporting']
        ],
        'dd' => [
            'categories' => [],
            'parents' => ['dl', 'div'],
            'children' => ['flow']
        ],
        'del' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['transparent']
        ],
        'details' => [
            'categories' => ['flow', 'interactive', 'palpable'],
            'parents' => ['flow'],
            'children' => ['summary', 'flow']
        ],
        'dfn' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'dialog' => [
            'categories' => ['flow'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'div' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow', 'dl'],
            'children' => ['flow']
        ],
        'dl' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['dt', 'dd', 'div', 'script-supporting']
        ],
        'dt' => [
            'categories' => [],
            'parents' => ['dl', 'div'],
            'children' => ['flow']
        ],
        'em' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'embed' => [
            'categories' => [
                'flow',
                'phrasing',
                'embedded',
                'interactive',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => []
        ],
        'fieldset' => [
            'categories' => ['flow', 'listed', 'form-associated', 'palpable'],
            'parents' => ['flow'],
            'children' => ['legend', 'flow']
        ],
        'figcaption' => [
            'categories' => [],
            'parents' => ['figure'],
            'children' => ['flow']
        ],
        'figure' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['figcaption', 'flow']
        ],
        'footer' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'form' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'h1' => [
            'categories' => ['flow', 'heading', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => ['phrasing']
        ],
        'h2' => [
            'categories' => ['flow', 'heading', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => ['phrasing']
        ],
        'h3' => [
            'categories' => ['flow', 'heading', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => ['phrasing']
        ],
        'h4' => [
            'categories' => ['flow', 'heading', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => ['phrasing']
        ],
        'h5' => [
            'categories' => ['flow', 'heading', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => ['phrasing']
        ],
        'h6' => [
            'categories' => ['flow', 'heading', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => ['phrasing']
        ],
        'head' => [
            'categories' => [],
            'parents' => ['html'],
            'children' => ['metadata']
        ],
        'header' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'hgroup' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['legend', 'summary', 'flow'],
            'children' => [
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'script-supporting'
            ]
        ],
        'hr' => [
            'categories' => ['flow'],
            'parents' => ['flow'],
            'children' => []
        ],
        'html' => [
            'categories' => [],
            'parents' => [],
            'children' => ['head', 'body']
        ],
        'i' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'iframe' => [
            'categories' => [
                'flow',
                'phrasing',
                'embedded',
                'interactive',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => []
        ],
        'img' => [
            'categories' => [
                'flow',
                'phrasing',
                'embedded',
                'interactive',
                'form-associated',
                'palpable'
            ],
            'parents' => ['phrasing', 'picture'],
            'children' => []
        ],
        'input' => [
            'categories' => [
                'flow',
                'phrasing',
                'interactive',
                'listed',
                'labelable',
                'submittable',
                'resettable',
                'form-associated',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => []
        ],
        'ins' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['transparent']
        ],
        'kbd' => [
            'categories' => ['flow', 'phrasing', 'palpable', 'preformatted'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'label' => [
            'categories' => ['flow', 'phrasing', 'interactive', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'legend' => [
            'categories' => [],
            'parents' => ['fieldset'],
            'children' => ['phrasing', 'heading']
        ],
        'li' => [
            'categories' => [],
            'parents' => ['ol', 'ul', 'menu'],
            'children' => ['flow']
        ],
        'link' => [
            'categories' => ['metadata', 'flow', 'phrasing'],
            'parents' => ['head', 'noscript', 'phrasing'],
            'children' => []
        ],
        'main' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'map' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['transparent', 'area']
        ],
        'mark' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'menu' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['li', 'script-supporting']
        ],
        'meta' => [
            'categories' => ['metadata', 'flow', 'phrasing'],
            'parents' => ['head', 'noscript', 'phrasing'],
            'children' => []
        ],
        'meter' => [
            'categories' => ['flow', 'phrasing', 'labelable', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'nav' => [
            'categories' => ['flow', 'sectioning', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'noscript' => [
            'categories' => ['metadata', 'flow', 'phrasing'],
            'parents' => ['head', 'phrasing'],
            'children' => []
        ],
        'object' => [
            'categories' => [
                'flow',
                'phrasing',
                'embedded',
                'interactive',
                'listed',
                'form-associated',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => ['transparent']
        ],
        'ol' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['li', 'script-supporting']
        ],
        'optgroup' => [
            'categories' => [],
            'parents' => ['select'],
            'children' => ['option', 'script-supporting']
        ],
        'option' => [
            'categories' => [],
            'parents' => ['select', 'datalist', 'optgroup'],
            'children' => ['text']
        ],
        'output' => [
            'categories' => [
                'flow',
                'phrasing',
                'listed',
                'labelable',
                'resettable',
                'form-associated',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'p' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['phrasing']
        ],
        'picture' => [
            'categories' => ['flow', 'phrasing', 'embedded', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['source', 'img', 'script-supporting']
        ],
        'pre' => [
            'categories' => ['flow', 'palpable', 'preformatted'],
            'parents' => ['flow'],
            'children' => ['phrasing']
        ],
        'progress' => [
            'categories' => ['flow', 'phrasing', 'labelable', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'q' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'rp' => [
            'categories' => [],
            'parents' => ['ruby'],
            'children' => ['text']
        ],
        'rt' => [
            'categories' => [],
            'parents' => ['ruby'],
            'children' => ['phrasing']
        ],
        'ruby' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing', 'rt', 'rp']
        ],
        's' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'samp' => [
            'categories' => ['flow', 'phrasing', 'palpable', 'preformatted'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'script' => [
            'categories' => [
                'metadata',
                'flow',
                'phrasing',
                'script-supporting',
                'preformatted'
            ],
            'parents' => ['head', 'phrasing', 'script-supporting'],
            'children' => []
        ],
        'search' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'section' => [
            'categories' => ['flow', 'sectioning', 'palpable'],
            'parents' => ['flow'],
            'children' => ['flow']
        ],
        'select' => [
            'categories' => [
                'flow',
                'phrasing',
                'interactive',
                'listed',
                'labelable',
                'submittable',
                'resettable',
                'form-associated',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => ['option', 'optgroup', 'script-supporting']
        ],
        'slot' => [
            'categories' => ['flow', 'phrasing'],
            'parents' => ['phrasing'],
            'children' => ['transparent']
        ],
        'small' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'source' => [
            'categories' => [],
            'parents' => ['picture', 'video', 'audio'],
            'children' => []
        ],
        'span' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'strong' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'style' => [
            'categories' => ['metadata', 'preformatted'],
            'parents' => ['head', 'noscript'],
            'children' => []
        ],
        'sub' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'summary' => [
            'categories' => [],
            'parents' => ['details'],
            'children' => ['phrasing', 'heading']
        ],
        'sup' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'table' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => [
                'caption',
                'colgroup',
                'thead',
                'tbody',
                'tfoot',
                'tr',
                'script-supporting'
            ]
        ],
        'tbody' => [
            'categories' => [],
            'parents' => ['table'],
            'children' => ['tr', 'script-supporting']
        ],
        'td' => [
            'categories' => [],
            'parents' => ['tr'],
            'children' => ['flow']
        ],
        'template' => [
            'categories' => [
                'metadata',
                'flow',
                'phrasing',
                'script-supporting'
            ],
            'parents' => [
                'metadata',
                'phrasing',
                'script-supporting',
                'colgroup'
            ],
            'children' => []
        ],
        'textarea' => [
            'categories' => [
                'flow',
                'phrasing',
                'interactive',
                'listed',
                'labelable',
                'submittable',
                'resettable',
                'form-associated',
                'palpable',
                'preformatted'
            ],
            'parents' => ['phrasing'],
            'children' => ['text']
        ],
        'tfoot' => [
            'categories' => [],
            'parents' => ['table'],
            'children' => ['tr', 'script-supporting']
        ],
        'th' => [
            'categories' => ['interactive'],
            'parents' => ['tr'],
            'children' => ['flow']
        ],
        'thead' => [
            'categories' => [],
            'parents' => ['table'],
            'children' => ['tr', 'script-supporting']
        ],
        'time' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'title' => [
            'categories' => ['metadata'],
            'parents' => ['head'],
            'children' => ['text']
        ],
        'tr' => [
            'categories' => [],
            'parents' => ['table', 'thead', 'tbody', 'tfoot'],
            'children' => ['th', 'td', 'script-supporting']
        ],
        'track' => [
            'categories' => [],
            'parents' => ['audio', 'video'],
            'children' => []
        ],
        'u' => [
            'categories' => ['flow', 'phrasing', 'palpable'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'ul' => [
            'categories' => ['flow', 'palpable'],
            'parents' => ['flow'],
            'children' => ['li', 'script-supporting']
        ],
        'var' => [
            'categories' => ['flow', 'phrasing', 'palpable', 'preformatted'],
            'parents' => ['phrasing'],
            'children' => ['phrasing']
        ],
        'video' => [
            'categories' => [
                'flow',
                'phrasing',
                'embedded',
                'interactive',
                'palpable'
            ],
            'parents' => ['phrasing'],
            'children' => ['source', 'track', 'transparent']
        ],
        'wbr' => [
            'categories' => ['flow', 'phrasing'],
            'parents' => ['phrasing'],
            'children' => []
        ]
    ];

    /**
     * Checks if the given node is a block level element.
     *
     * @param $node The node to check.
     */
    public static function isBlock(Node|Element $node): bool
    {
        $blocks = self::byFormattingContext('block');

        return in_array($node->localName, $blocks);
    }

    /**
     * Checks if the given node is a inline level element.
     *
     * @param $node The node to check.
     */
    public static function isInline(Node|Element $node): bool
    {
        $inlines = self::byFormattingContext('inline');

        return in_array($node->localName, $inlines);
    }

    /**
     * Checks if the given node belongs to the given category.
     *
     * @param $category The element category.
     * @param $node The node.
     */
    public static function inCategory(string $category, Node|Element $node)
    {
        $elements = self::byCategory($category);

        return in_array($node->localName, $elements);
    }

    /**
     * Takes an array of element categories and converts them to a normalized
     * array of element names.
     *
     * @param $categories The element categories.
     */
    public static function normalize(array $categories): array
    {
        $elements = [];

        foreach ($categories as $category) {
            $elements = array_merge($elements, self::byCategory($category));
        }

        return $elements;
    }

    /**
     * Returns the categories of an element.
     *
     * @param $name The element name.
     */
    public static function getCategories(string $name): array|null
    {
        if (!isset(self::$elements[$name])) {
            return null;
        }

        return self::$elements[$name]['categories'];
    }

    /**
     * Returns the allowed parents of an element.
     *
     * @param $name The element name.
     */
    public static function getParents(string $name): array|null
    {
        if (!isset(self::$elements[$name])) {
            return null;
        }

        return self::$elements[$name]['parents'];
    }

    /**
     * Returns the allowed children of an element.
     *
     * @param $name The element name.
     */
    public static function getChildren(string $name): array|null
    {
        if (!isset(self::$elements[$name])) {
            return null;
        }

        return self::$elements[$name]['children'];
    }

    /**
     * Returns an array of elements filtered by their content category.
     *
     * @param $category The category name.
     */
    public static function byCategory(string|null $category): array
    {
        if ($category === null) {
            if (!isset(self::$cache['uncategorized'])) {
                self::$cache['uncategorized'] = array_keys(
                    array_filter(self::$elements, function ($attributes) {
                        return empty($attributes['categories']);
                    })
                );
            }

            return self::$cache['uncategorized'];
        }

        if (!isset(self::$cache[$category])) {
            if (!in_array($category, self::$categories)) {
                return [];
            }

            self::$cache[$category] = array_keys(
                array_filter(self::$elements, function ($attributes) use (
                    $category
                ) {
                    return in_array($category, $attributes['categories']);
                })
            );
        }

        return self::$cache[$category];
    }

    /**
     * Returns an array of elements filtered by their formatting context.
     *
     * @param $context The context, either `block` or `inline`.
     */
    public static function byFormattingContext(string $context = 'block')
    {
        if (!isset(self::$cache[$context])) {
            $phrasing = self::byCategory('phrasing');

            if ($context !== 'block') {
                self::$cache[$context] = $phrasing;
            } else {
                $flow = self::byCategory('flow');
                self::$cache[$context] = array_diff($flow, $phrasing);
            }
        }

        return self::$cache[$context];
    }
}
