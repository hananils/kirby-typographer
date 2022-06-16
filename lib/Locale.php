<?php

namespace Hananils;

class Locale
{
    private $language = 'en';
    private $region = 'US';
    private $alternatives = false;
    private $quotes = null;

    public function __construct($locale, $alternatives = false)
    {
        $this->language = \Locale::getPrimaryLanguage($locale) ?? '';
        $this->region = \Locale::getRegion($locale) ?? '';
        $this->alternatives = $alternatives;
    }

    public function is($locale)
    {
        $language = \Locale::getPrimaryLanguage($locale);
        if ($language !== $this->language) {
            return false;
        }

        $region = \Locale::getRegion($locale);
        if (!empty($region) && $region !== $this->region) {
            return false;
        }

        return true;
    }

    public function in(array $locales = [])
    {
        $isIncluded = false;

        foreach ($locales as $locale) {
            if ($this->is($locale)) {
                $isIncluded = true;
            }
        }

        return $isIncluded;
    }

    public function setAlternatives($alternatives)
    {
        $this->alternatives = $alternatives;
        $this->setQuotes(true);
    }

    public function setQuotes($force = false)
    {
        if ($force !== true && $this->quotes !== null) {
            return;
        }

        $this->quotes = [
            'single' => [],
            'double' => []
        ];

        switch ($this->language) {
            case 'de':
                if ($this->region === 'CH') {
                    $this->quotes['single'] = ['‹', '›'];
                    $this->quotes['double'] = ['«', '»'];
                } elseif (
                    $this->alternatives === true ||
                    $this->alternatives === 'guillemets'
                ) {
                    $this->quotes['single'] = ['›', '‹'];
                    $this->quotes['double'] = ['»', '«'];
                } else {
                    $this->quotes['single'] = ['‚', '‘'];
                    $this->quotes['double'] = ['„', '“'];
                }
                break;
            case 'fr':
                $this->quotes['single'] = ['‹&#x202F;', '&#x202F;›'];
                $this->quotes['double'] = ['«&#x202F;', '&#x202F;»'];
                break;
            case 'en':
            default:
                $this->quotes['single'] = ['‘', '’'];
                $this->quotes['double'] = ['“', '”'];
        }
    }

    public function getDoubleOpeningQuote()
    {
        $this->setQuotes();

        return $this->quotes['double'][0];
    }

    public function getDoubleClosingQuote()
    {
        $this->setQuotes();

        return $this->quotes['double'][1];
    }

    public function getSingleOpeningQuote()
    {
        $this->setQuotes();

        return $this->quotes['single'][0];
    }

    public function getSingleClosingQuote()
    {
        $this->setQuotes();

        return $this->quotes['single'][1];
    }

    public function assessOpeningQuote($preceding, $following, $wildcard = '"')
    {
        $probability = 0;

        switch ($this->language) {
            case 'de':
                if (
                    $wildcard === "'" &&
                    preg_match('/^(n|ne|nen)\b/i', $following)
                ) {
                    $probability += -1;
                }
                break;
        }

        return $probability;
    }

    public function assessClosingQuote($preceding, $following, $wildcard = '"')
    {
        $probability = 0;

        if (preg_match('/\d$/', $preceding)) {
            $probability += -1;
        }

        switch ($this->language) {
            case 'de':
                if ($wildcard === "'" && preg_match('/[sßzx]$/i', $preceding)) {
                    $probability += -0.5;
                }
                break;
        }

        return $probability;
    }

    public function evaluateClosingQuotes($occurences, $wildcard = '"')
    {
        $evaluation = [];

        foreach ($occurences as $context) {
            $evaluation[] = $this->assessClosingQuote(
                $context['preceding'],
                $context['following'],
                $wildcard
            );
        }

        return $evaluation;
    }

    public function toString()
    {
        $string = $this->language;

        if ($this->region) {
            $string .= '-' . $this->region;
        }

        return $string;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
