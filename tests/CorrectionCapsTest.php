<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionCapsTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'caps' => ['Ein DIN-A4-Blatt', 'Ein <abbr>DIN</abbr>-A4-Blatt']
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
                ->caps()
                ->toString()
        );
    }
}
