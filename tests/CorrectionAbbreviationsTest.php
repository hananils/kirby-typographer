<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionAbbreviationsTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'German abbreviations with German locale' => [
                'z. B. usw. u. A. w. g.',
                'z. B. usw. u. A. w. g.',
                'de_DE'
            ],
            'German abbreviations with English locale' => [
                'z. B. usw. u. A. w. g.',
                'z. B. usw. u. A. w. g.',
                'en_GB'
            ]
        ];
    }

    /**
     * @dataProvider texts
     */
    public function testCorrection($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale);
        $typographer->setFlow('inline');

        $this->assertSame(
            $expected,
            $typographer
                ->parse($input)
                ->abbreviations()
                ->toString()
        );
    }
}
