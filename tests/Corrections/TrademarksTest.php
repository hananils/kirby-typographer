<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'trademark' => ['Trademark(tm)', 'Trademark™'],
    'registered' => ['Registered(r)', 'Registered®'],
    'copyright with year' => [
        '(c) 2022 Any Company',
        '©&nbsp;2022 Any Company'
    ],
    'copyright without year' => ['(c) Any Company', '© Any Company']
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->trademarks()->toHtml())
        ->toBe($expected);
})->with('texts');
