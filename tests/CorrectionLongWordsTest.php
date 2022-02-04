<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionLongWordsTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'long words' => [
                'Ein Satz mit superkalifragilistisch expiallegorischen langen Wörtern.',
                'Ein Satz mit <span class="long-word">superkalifragilistisch</span> <span class="long-word">expiallegorischen</span> langen Wörtern.',
                'de-DE'
            ]
        ];
    }

    /**
     * @dataProvider texts
     */
    public function testCorrection($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale, 'inline');

        $this->assertSame(
            $expected,
            $typographer
                ->parse($input)
                ->longWords()
                ->toString()
        );
    }
}
