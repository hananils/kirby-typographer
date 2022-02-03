<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionTrademarksTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'trademark' => ['Trademark(tm)', 'Trademark™'],
            'registered' => ['Registered(r)', 'Registered®'],
            'copyright with year' => [
                '(c) 2022 Any Company',
                '© 2022 Any Company'
            ],
            'copyright without year' => ['(c) Any Company', '© Any Company']
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
                ->trademarks()
                ->toString()
        );
    }
}
