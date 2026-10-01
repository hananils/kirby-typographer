[![Typographer for Kirby CMS](header.png)](https://kirby.hananils.de/plugins/typographer)

Typographer is a plugin that offers methods to apply advanced typographic rules to your content. Besides handling common cases like quotes, apostrophes or dashes out of the box, it allows for the creation of custom corrections and helps to typeset your content inside your templates and snippets smoothly.

> [!TIP]
> Oh, hi, long time no see 👋 Typographer has been around for a long time but you’ve only ever seen version 0.2. This is version 4.0. Yes, that’s right. Things have changed, please do not directly update in case you used the old version before. Read the docs to see what changed first.
> 
> If you need DOM manipulation, please check out our new [Designer](https://kirby.hananils.test/plugins/designer) plugin.

## More than just smart pants.

Consistency and professional tone made easy and efficient for your editors: Fine-tune your Typographer setup with custom corrections to work hand-in-hand with an established style guide.

[Learn more about custom corrections](https://kirby.hananils.test/plugins/typographer/creating-custom-corrections).

### Hallo Welt! Hei verden! Merhaba dünya!

Of international interest: Typographer currently bundles typographic rules for 70 locales. C’est génial, n’est-ce pas?

[Browse The Long List of Languages](https://kirby.hananils.test/plugins/typographer/locales).

## Included corrections

Typographer bundles a set of corrections that can be applied individually. If no corrections are applied, Typographer will back to following default set:

- [Abbreviations](https://kirby.hananils.test/plugins/typographer/included-corrections#abbreviations)
- [Apostrophes](https://kirby.hananils.test/plugins/typographer/included-corrections#apostrophes)
- [Caps](https://kirby.hananils.test/plugins/typographer/included-corrections#caps)
- [Dashes](https://kirby.hananils.test/plugins/typographer/included-corrections#dashes)
- [Ellipsis](https://kirby.hananils.test/plugins/typographer/included-corrections#ellipsis)
- [Highlight](https://kirby.hananils.test/plugins/typographer/included-corrections#highlight)
- [Links](https://kirby.hananils.test/plugins/typographer/included-corrections#links)
- [Lists](https://kirby.hananils.test/plugins/typographer/included-corrections#lists)
- [Math](https://kirby.hananils.test/plugins/typographer/included-corrections#math)
- [Middot](https://kirby.hananils.test/plugins/typographer/included-corrections#middot)
- [Permalinks](https://kirby.hananils.test/plugins/typographer/included-corrections#permalinks)
- [Primes](https://kirby.hananils.test/plugins/typographer/included-corrections#primes)
- [Quotes](https://kirby.hananils.test/plugins/typographer/included-corrections#quotes)
- [Slashes](https://kirby.hananils.test/plugins/typographer/included-corrections#slashes)
- [Titled lists](https://kirby.hananils.test/plugins/typographer/included-corrections#titled-lists)
- [Trademarks](https://kirby.hananils.test/plugins/typographer/included-corrections#trademarks)
- [Uuid](https://kirby.hananils.test/plugins/typographer/included-corrections#uuid)

In addition to these defaults, there are several optional corrections that can be applied manually:

- [Currencies](https://kirby.hananils.test/plugins/typographer/included-corrections#currencies)
- [Long words](https://kirby.hananils.test/plugins/typographer/included-corrections#long-words)
- [Namespaces](https://kirby.hananils.test/plugins/typographer/included-corrections#namespaces)
- [Punctuation](https://kirby.hananils.test/plugins/typographer/included-corrections#punctuation)
- [Punctuation placement](https://kirby.hananils.test/plugins/typographer/included-corrections#punctuation-placement)
- [Ranges](https://kirby.hananils.test/plugins/typographer/included-corrections#ranges)
- [Short paragraphs](https://kirby.hananils.test/plugins/typographer/included-corrections#short-paragraphs)
- [Widont](https://kirby.hananils.test/plugins/typographer/included-corrections#widont)

> \[!important\] Please note that this plugin makes use of the latest `Dom` additions in PHP 8.4 and their extensions in PHP 8.5. Thus **PHP 8.5 is a requirement** for this plugin.

## Installation

By default, plugins in Kirby reside in a special folder located at `/site/plugins`. Each plugin is installed in its proprietary subfolder. This installation can be handled in four different ways: you can either install them manually or manage them using Kirby CLI, Git submodules or Composer. You can install Typographer either way and should choose the method suiting your project best.

Please note that all examples given here assume you are using the default plugin root. [If you changed your plugin root](https://getkirby.com/docs/reference/system/roots/plugins), e. g. with a custom folder setup, you’ll also have to adjust the paths given in this guide. For further information on how to manage plugins, please read the [official Kirby plugin introduction](https://getkirby.com/docs/guide/plugins/plugin-basics).

### Download

Download and copy this repository to `/site/plugins/typographer`.

### Kirby CLI

```shell
kirby plugin:install hananils/kirby-typographer
```

### Git submodule

```bash
git submodule add \
    https://github.com/hananils/kirby-typographer.git \
    site/plugins/typographer
```

### Composer

```shell
composer require hananils/kirby-typographer
```

## Documentation

[![Find all documentation at kirby.hananils.de](footer.png)](https://kirby.hananils.de/plugins/typographer)

Where possible, files contain inline annotations. For extended documentation, please visit our dedicated plugin site at [kirby.hananils.de/​plugins/​typographer](https://kirby.hananils.de/plugins/typographer).

### Guides

- [Using Typographer](https://kirby.hananils.de/plugins/typographer/initialising-typographer)
- [Applying corrections](https://kirby.hananils.de/plugins/typographer/applying-corrections)
- [Locales](https://kirby.hananils.de/plugins/typographer/locales)
- [Included corrections](https://kirby.hananils.de/plugins/typographer/included-corrections)
- [Custom corrections](https://kirby.hananils.de/plugins/typographer/creating-custom-corrections)

### Reference

- [Field Methods](https://kirby.hananils.de/plugins/typographer/field-methods)
- [Blocks Methods](https://kirby.hananils.de/plugins/typographer/blocks-methods)
- [Block Methods](https://kirby.hananils.de/plugins/typographer/block-methods)
- [Helpers](https://kirby.hananils.de/plugins/typographer/helpers)
- [Hooks](https://kirby.hananils.de/plugins/typographer/hooks)

## License

This plugin is provided freely under the [MIT license](https://kirby.hananils.de/plugins/typographer/license) by [hana+nils · Büro für Gestaltung](https://kirby.hananils.de). We create visual designs for digital and analog media.