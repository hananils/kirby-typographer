<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'quote with long words (unwrapped)' => [
        '<p>"Dies ist ein Zitat mit \'superkalifragilistisch expiallegorischen\' langen Wörtern."</p>',
        '<p>„Dies ist ein Zitat mit ‚superkalifragilistisch expiallegorischen‘ langen Wörtern.“</p>',
        'de-DE'
    ],
    'quote with apostrophes' => [
        '<p>"Dies ist Rock\'n\'Roll."</p>',
        '<p>„Dies ist Rock\'n\'Roll.“</p>',
        'de-DE'
    ]
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->quotes()->toHtml())
        ->toBe($expected);
})->with('texts');

dataset('alternatives', [
    'quote with long words (unwrapped)' => [
        '<p>"Dies ist ein Zitat mit \'superkalifragilistisch expiallegorischen\' langen Wörtern."</p>',
        '<p>»Dies ist ein Zitat mit ›superkalifragilistisch expiallegorischen‹ langen Wörtern.«</p>',
        'de-DE'
    ]
]);

test('alternatives', function ($input, $expected, $locale = 'en-US') {
    expect(
        Typographer::fromString($input, $locale)
            ->quotes(['style' => 'alternatives'])
            ->toHtml()
    )
        ->toBe($expected);
})->with('alternatives');
