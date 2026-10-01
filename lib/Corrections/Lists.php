<?php

namespace Hananils\Typographer\Corrections;

use Hananils\Typographer\Correction;

/**
 * Lists adds length information to ordered and unordered lists. This allows for
 * different styling based on content length or list item count.
 *
 * # Styling based on list density
 *
 * Lists with only short content blocks will get an additional class so that you
 * can add or remove margins between list items depending on the content length.
 *
 * ```html
 * <ul class="dense">
 *     <li>one</li>
 *     <li>two</li>
 *     <li>three</li>
 * </ul>
 * ```
 *
 * ```css
 * li:not(.dense) {
 *     margin-top: 1em;
 * }
 * ```
 *
 * # Ordered list
 *
 * Styling list markers is limited in CSS. If you like to have control over your
 * number formatting you can work around this by making use of pseudo elements
 * and counters. This correction adds the number of digits of the largest list
 * item number to a specific `data-digits` attribute. Based on this value, you
 * can adjust number sizing or list indentation. The attribute take starting
 * numbers, e. g. `start="100"` into account.
 *
 * ```html
 * <ol data-digit="1">
 *     <li>Starting with 10, the digit count will be raised to 2</li>
 *     <li>Starting with 100, the digit count will be raise to 3</li>
 *     <li>And so on with 1000, 10000 …</li>
 * </ol>
 * ```
 */
class Lists extends Correction
{
    /**
     * Formats lists.
     *
     * This method is called when applying `$typographer->lists()`.
     */
    public function apply()
    {
        $this->inspector
            ->select('ul, ol')
            ->toSelection()
            ->filterBy(
                'categoriesIncludingAncestors',
                'excludes',
                ['preformatted']
            )
            ->each(function ($list) {
                $length = strlen($list->text());
                $items = $list->query('li');
                $start = intval($list->getAttribute('start')) ?? 0;
                $count = $start + $items->count();
                $limit = $this->options->get('limit', 75);

                if ($list->is('ol')) {
                    $list->dataset([
                        'digits' => strlen(strval($count))
                    ]);
                }

                if ($items->isNotEmpty() && ($length / $count) < $limit) {
                    $list->addClass($this->options->get('classname', 'dense'));
                }
            });
    }
}
