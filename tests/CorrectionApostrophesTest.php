<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class CorrectionApostrophesTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'apostrophe at the beginning of a word with German locale' => [
                'So \'nen Mist!',
                'So ’nen Mist!',
                'de_DE'
            ],
            'apostrophe within a word with German locale' => [
                'Grimm\'sche Märchen',
                'Grimm’sche Märchen',
                'de_DE'
            ],
            'apostrophe a the end of a word with German locale' => [
                'Nils\' Test',
                'Nils’ Test',
                'de_DE'
            ],
            'shortened "and" with spaces with German locale' => [
                'Rock \'n\' Roll',
                'Rock ’n’ Roll',
                'de_DE'
            ],
            'shortened "and" without spaces with German locale' => [
                'Rock\'n\'Roll',
                'Rock’n’Roll',
                'de_DE'
            ],
            'shortened decade with German locale' => [
                'in den \'80ern',
                'in den ’80ern',
                'de_DE'
            ],
            'shortened decade with English locale' => [
                'in the \'80s',
                'in the ’80s',
                'en_GB'
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
                ->apostrophes()
                ->toString()
        );
    }
}
