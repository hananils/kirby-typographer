<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'German abbreviations with German locale' => [
        'z. B. usw. u. A. w. g.',
        'z. B. usw. u. A. w. g.',
        'de_DE'
    ]
]);

test('abbreviations', function ($input, $expected, $locale = 'en-US') {
    $typographer = Typographer::fromString($input, $locale);

    expect($typographer->abbreviations()->toHtml())->toBe($expected);
})->with('texts');
