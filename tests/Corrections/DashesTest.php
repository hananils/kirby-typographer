<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'single hyphen to en dash' => [
        '<p>Ein Gedanke - noch einer.</p>',
        '<p>Ein Gedanke&nbsp;– noch einer.</p>'
    ],
    'double hyphen to en dash' => [
        '<p>Ein Gedanke -- noch einer.</p>',
        '<p>Ein Gedanke&nbsp;– noch einer.</p>'
    ],
    'date range' => ['<p>2011-2022</p>', '<p>2011–2022</p>'],
    'hyphen' => [
        '<p>Donau-Dampfschiffahrts-Gesellschaft</p>',
        '<p>Donau-Dampfschiffahrts-Gesellschaft</p>'
    ]
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->dashes()->toHtml())
        ->toBe($expected);
})->with('texts');
