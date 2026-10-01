<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'primes' => ['118° 19\' 43.5"', '118° 19′ 43.5″']
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->primes()->toHtml())
        ->toBe($expected);
})->with('texts');
