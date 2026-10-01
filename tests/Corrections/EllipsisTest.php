<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'ellipsis' => ['Das ist eine sch... Sache ...', 'Das ist eine sch… Sache …']
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->ellipsis()->toHtml())
        ->toBe($expected);
})->with('texts');
