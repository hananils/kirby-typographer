![Kirby Typographer](.github/title.png)

**Typographer** is a plugin for [Kirby 3](https://getkirby.com) to apply microtypographic rules to texts. It used `DomDocument` under the hood to understand the semantic of the given text and respecting `lang` attributes to adjust the used locale contextually.

Typographer applies the following corrections:

-   opening and closing quotes (primary and secondary)
-   apostrophes
-   primes
-   dashed
-   ellipses
-   multiplication signs
-   trademark signs

Further more it applies Widont rules and detects abbreviations and long words.

**Note:** Typographic rules often require an understanding of the underlying text which cannot be achieved automatically. Thus, the plugin will incorrectly match characters or apply incorrect rules. Good text layout still requires manual proofreading.

Currently, Typographer offers custom rules for German, English and French. This plugin was created with a German language background and might not know yet about rules specific to other languages.

## Examples

```md
"We are at 41° 43' 32.99"", said the captain while pointing on the map.

<p lang="de">"Wir brauchen auch 'Zitate in Zitaten' - nicht, dass das untergeht!" ergänzte der Grafiker.</p>

The programmer added: "Can we please make sure that my code stays intact?" Of course, we can: `$test = "This is text in straight quotes with ... dots"`.
```

```php
<?= $page->text()->typographer() ?>
```

```html
<p>“We are at 41° 43′ 32.99″”, said the captain while pointing on the map.</p>

<p lang="de">„Wir brauchen auch ‚Zitate in Zitaten‘ – nicht, dass das untergeht!“ ergänzte der Grafiker.</p>

<p>The <span class="long-word">programmer</span> added: “Can we please make sure that my code stays intact?” Of course, we can: <code>$test = "This is text in straight quotes with ... dots"</code>.</p
```

Typographer also allows for additional content manipulations:

```php
<?= $page->text()->typographer()->filter('p[1]') ?>
```

```html
<p>“We are at 41° 43′ 32.99″”, said the captain while pointing on the map.</p>
```

## Installation

### Download

Download and copy this repository to `/site/plugins/typographer`.

### Git submodule

```
git submodule add https://github.com/hananils/kirby-typographer.git site/plugins/typographer
```

### Composer

```
composer require hananils/kirby-typographer
```

# Methods

Typographer is available as a field method as well as a blocks method.

## $field->typographer($flow, $convert)

Applies typographic rules to the given field.

-   **`$flow`:** Determines whether the content is considered to be of block or inline flow. If no flow is set, the block or inline context is guessed from the field definition in the blueprint (this will reduce performance if used too often and on complex pages).
-   **`$convert`** Determines whether the content should be converted to Markdown before applying typographic rules, respecting the flow setting. Defaults to `true`.

## $blocks->typographer() or $block->typographer()

Applies typographic rules to the blocks.

## Manipulations

There are several methods to fine-tune the output that can be chained with this main method:

### $field->typographer()->ignore($ignore)

Sets elements that should be ignore when applying typographic changes, especially helpful for code block.

-   **`$ignore`:** Array of elements that should be skipped. Defaults to `['pre', 'code']`.

### $field->typographer()->attributes($query, $attributes)

Queries elements and sets the given attributes:

-   **`$query`:** The xPath query to find the elements.
-   **`$attributes`:** An array of attributes with the names as key.

```php
$page
    ->text()
    ->typographer()
    ->setAttributes('p', [
        'class' => 'typographic'
    ]);
```

### $field->typographer()->attribute($query, $name, $value)

Queries elements and sets a single attribute:

-   **`$query`:** The xPath query to find the elements.
-   **`$name`:** The attribute name.
-   **`$value`:** The attribute value.

```php
$page
    ->text()
    ->typographer()
    ->setAttribute('p', 'class', 'typographic');
```

### $field->typographer()->rename($query, $name)

Queries elements and changes their name:

-   **`$query`:** The xPath query to find the elements.
-   **`$name`:** The new element name.

```php
$page
    ->text()
    ->typographer()
    ->setName('bold', 'strong');
```

### $field->typographer()->level($level)

Changes the starting point of the headline hierarchy:

-   **`$level`:** The new starting point of the headline hierarchy. Defaults to `2`.

```php
// All h1 will be rendered as h3, all h2 will be rendered as h4 and so on …
$page
    ->text()
    ->typographer()
    ->level(3);
```

### $field->typographer()->filter($query)

Filters the resulting markup by the given query:

-   **`$query`:** The xPath query to filter the output.

```php
// Returns the first paragraph only
$page
    ->text()
    ->typographer()
    ->filter('p[1]');
```

# Additional field methods

## toDefinition()

Field method returning the field definitions from the related blueprint.

## toFlow($flow)

Field method converting the given field value to block or inline content using Markdown based on the selected flow.

-   **`$flow`:** the used flow context, either `block` or `inline`. Defaults to `block`.

# Helper

## typographer($html, $flow = 'block', $locale = null)

Helper which can be used with custom markup directly applying typographic rules to the given content:

-   **`$html`:** The HTML to be parsed. Expected to be valid.
-   **`$flow`:** Determines whether the content is considered to be of block or inline flow. Defaults to `block`.
-   **`$locale`:** The locale used to select the typographic rules, e. g. `de`, `de-CH`, `en-US`.

# Handling hyphenation

In recent years, browsers have become very good in hyphenating text if you set `hyphens: auto;` in your CSS. This helps a lot with responsive designs and headline on mobile. Nevertheless, setting `hyphens: auto;` often results in word breaks everywhere which will make text hard to read.

Actually, all that's need is hyphenation on long words that might overflow. Thus, Typographer will wrap long word in `span` elements with the class `.long-word` which allows you to apply hyphenation to these long words only:

```css
.long-word {
    hyphens: auto:
}
```

# Inspiration and similar approaches

There are a lot of different approaches to typography each suiting different needs, especially focussing on different languages. These libraries have been an inspiration for this plugin:

-   https://github.com/frankrausch/Typographizer (not PHP)
-   https://github.com/jolicode/JoliTypo (PHP)
-   https://github.com/davidmerfield/Typeset (JavaScript)

We found these libraries in this very helpful collection by Frank Rausch: https://gist.github.com/frankrausch/1aa9c80741abd87a450a5bcb2a1687c2.

# License

This plugin is provided freely under the [MIT license](LICENSE.md) by [hana+nils · Büro für Gestaltung](https://hananils.de). We create visual designs for digital and analog media.
