<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionMathTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'times sign' => ['21 x 29,7 cm', '21 × 29,7 cm']
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
                ->math()
                ->toString()
        );
    }
}
