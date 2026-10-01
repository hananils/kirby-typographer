<?php

use Dom\HTMLDocument;
use Hananils\Document\Inspector;
use Hananils\Typographer\Locale;
use Hananils\Typographer\Typographer;

arch()->preset()->php();
arch()->preset()->security();


$inspector = new Inspector('<h1>This is a headline</h1>');
$locale = new Locale('en');
$options = [
    'caps' => [
        'classname' => 'uppercase'
    ]
];

test('Typographer construction', function () use (
    $inspector,
    $locale,
    $options
) {
    $typographer = new Typographer($inspector, $locale, $options);

    expect($typographer->inspector)->toEqual($inspector);
    expect($typographer->locale)->toEqual($locale);
    expect($typographer->locale->get())->toBe('en');
    expect($typographer->options->getAll())->toEqual($options);
});

test('Typographer from string', function () {
    $html = '<p>Create Typographer from string.</p>';

    $typographer = Typographer::fromString($html);
    expect($typographer->inspector)->toBeInstanceOf(Inspector::class);
    expect($typographer->inspector->toHtml())->toBe($html);
    expect($typographer->locale->get())->toBe('en');
    expect($typographer->options->getAll())->toBe([]);

    $typographer = Typographer::fromString($html, 'de');
    expect($typographer->locale->get())->toBe('de');

    $typographer = Typographer::fromString($html, options: ['key' => 'value']);
    expect($typographer->locale->get())->toBe('en');
    expect($typographer->options->getAll())->toBe(['key' => 'value']);
});

test('Typographer from document', function () {
    $html = '<p>Create Typographer from document.</p>';
    $document = \Dom\HTMLDocument::createFromString(
        "<!doctype html><html><head></head><body>$html</body></html>"
    );

    $typographer = Typographer::fromDocument($document, false);
    expect($typographer->toHtml())
        ->toBe("<!DOCTYPE html>\n<html><head></head><body>$html</body></html>");

    $typographer = Typographer::fromDocument($document, true);
    expect($typographer->toHtml())->toBe($html);

    expect($typographer->inspector)->toBeInstanceOf(Inspector::class);
    expect($typographer->inspector->toHtml())->toBe($html);
    expect($typographer->locale->get())->toBe('en');
    expect($typographer->options->getAll())->toBe([]);

    $typographer = Typographer::fromDocument($document, true, 'de');
    expect($typographer->locale->get())->toBe('de');

    $typographer = Typographer::fromDocument($document, true, options: ['key' => 'value']);
    expect($typographer->locale->get())->toBe('en');
    expect($typographer->options->getAll())->toBe(['key' => 'value']);
});
