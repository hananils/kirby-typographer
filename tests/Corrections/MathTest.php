<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'times sign' => ['21 x 29,7 cm', '21&nbsp;×&nbsp;29,7 cm']
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->math()->toHtml())
        ->toBe($expected);
})->with('texts');
