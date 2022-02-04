<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionWidontTest extends PHPUnit\Framework\TestCase
{
    public function testCorrection()
    {
        $typographer = new Typographer('de_DE', 'inline');
        $typographer->setFlow('inline');

        $this->assertSame(
            'Dies ist ein Satz.',
            $typographer
                ->parse('Dies ist ein Satz.')
                ->widont()
                ->toString()
        );

        $typographer = new Typographer('de_DE', 'block');

        $this->assertSame(
            '<p>Dies ist ein Satz.</p>',
            $typographer
                ->parse('Dies ist ein Satz.')
                ->widont()
                ->toString()
        );
    }
}
