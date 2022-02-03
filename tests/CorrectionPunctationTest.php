<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionPunctationTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'punctation with French locale' => [
                '"Ceci est une citation : il y a quelques signes de ponctuation !"',
                '"Ceci est une citation : il y a quelques signes de ponctuation !"',
                'fr_FR'
            ],
            'punctation with German locale' => [
                '"Dies ist ein Zitat: mit einigen Satzzeichen!"',
                '"Dies ist ein Zitat: mit einigen Satzzeichen!"',
                'de_DE'
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
                ->punctation()
                ->toString()
        );
    }
}
