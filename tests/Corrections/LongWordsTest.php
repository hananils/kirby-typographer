<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'long words' => [
        'Ein Satz mit superkalifragilistisch expiallegorischen langen Wörtern.',
        'Ein Satz mit <span class="long-word">superkalifragilistisch</span> <span class="long-word">expiallegorischen</span> langen Wörtern.',
        'de-DE'
    ]
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->longWords()->toHtml())
        ->toBe($expected);
})->with('texts');
