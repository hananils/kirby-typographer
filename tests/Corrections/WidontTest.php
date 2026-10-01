<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'plain text' => ['Dies ist ein Satz.', 'Dies ist ein&nbsp;Satz.'],
    'HTML' => ['<p>Dies ist ein Satz.</p>', '<p>Dies ist ein&nbsp;Satz.</p>']
]);

test('windonts', function (string $input, string $expected, string $locale = 'de_DE') {
    $typographer = Typographer::fromString($input, $locale);

    expect($typographer->widont()->toHtml())->toBe($expected);
})->with('texts');
