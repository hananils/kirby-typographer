<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'caps' => ['Ein DIN-A4-Blatt', 'Ein <abbr>DIN</abbr>-A4-Blatt']
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->caps()->toHtml())
        ->toBe($expected);
})->with('texts');
