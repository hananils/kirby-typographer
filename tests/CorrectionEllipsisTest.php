<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionEllipsisTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'ellipsis' => [
                'Das ist eine sch... Sache ...',
                'Das ist eine sch… Sache …'
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
                ->ellipsis()
                ->toString()
        );
    }
}
