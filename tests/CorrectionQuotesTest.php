<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionQuotesTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'quote with long words (unwrapped)' => [
                '<p>"Dies ist ein Zitat mit \'superkalifragilistisch expiallegorischen\' langen Wörtern."</p>',
                '<p>„Dies ist ein Zitat mit ‚superkalifragilistisch expiallegorischen‘ langen Wörtern.“</p>',
                'de-DE'
            ]
        ];
    }

    public function alternatives()
    {
        return [
            'quote with long words (unwrapped)' => [
                '<p>"Dies ist ein Zitat mit \'superkalifragilistisch expiallegorischen\' langen Wörtern."</p>',
                '<p>»Dies ist ein Zitat mit ›superkalifragilistisch expiallegorischen‹ langen Wörtern.«</p>',
                'de-DE'
            ]
        ];
    }

    /**
     * @dataProvider texts
     */
    public function testCorrection($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale);

        $this->assertSame(
            $expected,
            $typographer
                ->parse($input)
                ->quotes()
                ->toString()
        );
    }

    /**
     * @dataProvider alternatives
     */
    public function testAlternatives($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale);

        $this->assertSame(
            $expected,
            $typographer
                ->parse($input)
                ->alternatives()
                ->quotes()
                ->toString()
        );
    }
}
