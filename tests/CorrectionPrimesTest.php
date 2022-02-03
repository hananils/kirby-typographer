<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionPrimesTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'primes' => ['118° 19\' 43.5"', '118° 19′ 43.5″']
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
                ->primes()
                ->toString()
        );
    }
}
