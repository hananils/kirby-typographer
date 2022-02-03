<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionDashesTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'single hyphen to en dash' => [
                '<p>Ein Gedanke - noch einer.</p>',
                '<p>Ein Gedanke – noch einer.</p>'
            ],
            'double hyphen to en dash' => [
                '<p>Ein Gedanke -- noch einer.</p>',
                '<p>Ein Gedanke – noch einer.</p>'
            ],
            'date range' => ['<p>2011-2022</p>', '<p>2011–2022</p>'],
            'hyphen' => [
                '<p>Donau-Dampfschiffahrts-Gesellschaft</p>',
                '<p>Donau-Dampfschiffahrts-Gesellschaft</p>'
            ]
        ];
    }

    /**
     * @dataProvider texts
     */
    public function testCorrection($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale);
        $typographer->setFlow('block');

        $this->assertSame(
            $expected,
            $typographer
                ->parse($input)
                ->dashes()
                ->toString()
        );
    }
}
