<?php

use Hananils\Typographer\Typographer;

dataset('texts', [
    'punctation with French locale' => [
        '<p>"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
        '<p>"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
        'fr_FR'
    ],
    'punctation with German in French locale' => [
        '<p lang="de">"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
        '<p lang="de">"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
        'fr_FR'
    ],
    'punctation with French in German locale' => [
        '<p lang="fr">"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
        '<p lang="fr">"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
        'de_DE'
    ],
    'punctation with German locale' => [
        '"Dies ist ein Zitat : mit einigen Satzzeichen!"',
        '"Dies ist ein Zitat : mit einigen Satzzeichen!"',
        'de_DE'
    ]
]);

test('correction', function ($input, $expected, $locale = 'en-US') {
    expect(Typographer::fromString($input, $locale)->punctuation()->toHtml())
        ->toBe($expected);
})->with('texts');
