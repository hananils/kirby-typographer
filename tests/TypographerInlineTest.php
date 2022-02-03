<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class TypographerInlineTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'simple sentence with German locale' => [
                'Dies ist ein einfacher Satz.',
                'Dies ist ein einfacher Satz.',
                'de-DE'
            ],

            /**
             * Mixture of primary and secondary quotation
             *
             * Note: French sets quotes differently (and wrong from
             * the German perspective) because it lacks rules to detect
             * genitives so far. See the Locale class. So while the result looks
             * wrong it's actually expected behaviour. English is only correct by
             * chance because single closing quotes and apostrophes look the same.
             */
            'primary and secondary quotes with German locale' => [
                '"Wo ist hier \'Nils\' Zitatende\'?"',
                '„Wo ist hier ‚Nils’ Zitatende‘?“',
                'de-DE'
            ],
            'primary and secondary quotes with English locale' => [
                '"Wo ist hier \'Nils\' Zitatende\'?"',
                '“Wo ist hier ‘Nils’ Zitatende’?”',
                'en-US'
            ],
            'primary and secondary quotes with French locale' => [
                '"Wo ist hier \'Nils\' Zitatende\'?"',
                '« Wo ist hier ‹ Nils › Zitatende’? »',
                'fr-FR'
            ],

            /**
             * Wörtliche Rede, siehe https://de.wikipedia.org/wiki/Direkte_Rede (CC BY-SA 3.0)
             */
            'direct speech (multiple paragraphs) with German locale' => [
                'Er rief: "Guck mal, es schneit!" Sie sagte: "Guck mal, es schneit!" Er meinte: "Es schneit." "Schau mal, es schneit!", rief er. "Schau mal, es schneit!", sagte sie. "Es schneit!", meinte er. "Zieh deine Winterjacke an", mahnte der Vater, "sonst wird dir kalt." "Sie sagte: "Es schneit und mir ist kalt." – "Hast du deine Winterjacke angezogen?", fragte er.',
                'Er rief: „Guck mal, es schneit!“ Sie sagte: „Guck mal, es schneit!“ Er meinte: „Es schneit.“ „Schau mal, es schneit!“, rief er. „Schau mal, es schneit!“, sagte sie. „Es schneit!“, meinte er. „Zieh deine <span class="long-word">Winterjacke</span> an“, mahnte der Vater, „sonst wird dir kalt.“ „Sie sagte: „Es schneit und mir ist kalt.“ – „Hast du deine <span class="long-word">Winterjacke</span> angezogen?“, fragte er.',
                'de-DE'
            ],
            'direct speech (inset) with German locale' => [
                '"Da vorne", sagt Lello, "ist ja schon die Villa."',
                '„Da vorne“, sagt Lello, „ist ja schon die Villa.“',
                'de-DE'
            ],
            'direct speech (two sentences) with German locale' => [
                '"Ich weiß noch, wie du Nala bekommen hast", sagt Lilli. "Du hast dich damals riesig gefreut."',
                '„Ich weiß noch, wie du Nala bekommen hast“, sagt Lilli. „Du hast dich damals riesig gefreut.“',
                'de-DE'
            ],
            'direct speech (question + sentence) with German locale' => [
                '"Weißt du noch, wie du Nala bekommen hast?", fragt Lilli. "Du hast dich damals riesig gefreut."',
                '„Weißt du noch, wie du Nala bekommen hast?“, fragt Lilli. „Du hast dich damals riesig gefreut.“',
                'de-DE'
            ],

            /**
             * Examples with contextual locale where the output should be the same
             * independent of the global locale.
             */
            'contextual locale with English locale' => [
                '"We are at 118° 19\' 43.5"", said the captain while pointing on the map.',
                '“We are at 118° 19′ 43.5″”, said the captain while pointing on the map.',
                'en-US'
            ],

            'contextual locale with German locale' => [
                '"Wir brauchen auch \'Zitate in Zitaten\' - nicht, dass das untergeht!" ergänzte der Grafiker.',
                '„Wir brauchen auch ‚Zitate in Zitaten‘ – nicht, dass das untergeht!“ ergänzte der Grafiker.',
                'de-DE'
            ],

            'contextual locale with English locale 2' => [
                'The programmer added: "Can we please make sure that my code stays intact?" Of course, we can: <code>let test = "This is text in straight quotes with ... dots"</code>.',
                'The <span class="long-word">programmer</span> added: “Can we please make sure that my code stays intact?” Of course, we can: <code>let test = "This is text in straight quotes with ... dots"</code>.',
                'en-US'
            ],

            /**
             * Inline apostrophes
             */
            'inline apostrophs with German locale' => [
                'Rock\'n\'Roll, Grimm\'sche Märchen, "Kennt Ihr die \'Grimm\'schen Märchen\'?"',
                'Rock’n’Roll, Grimm’sche Märchen, „Kennt Ihr die ‚Grimm’schen Märchen‘?“',
                'de-DE'
            ],
            'inline apostrophs with French locale' => [
                'Rock\'n\'Roll, Grimm\'sche Märchen, "Kennt Ihr die \'Grimm\'schen Märchen\'?"',
                'Rock’n’Roll, Grimm’sche Märchen, « Kennt Ihr die ‹ Grimm’schen Märchen ›? »',
                'fr-FR'
            ],

            /**
             * Abbreviations with apostrophs
             */
            'opening apostroph with German locale' => [
                'Was für \'n Blödsinn! Kommen S\' nur herein!',
                'Was für ’n Blödsinn! Kommen S’ nur herein!',
                'de_DE'
            ],

            /**
             * Math
             */
            'times signs' => [
                'Ein DIN-A4-Blatt ist 21 x 29,7 cm groß.',
                'Ein <abbr>DIN</abbr>-A4-Blatt ist 21 × 29,7 cm groß.'
            ],

            /**
             * Trademarks
             */
            'trademarks' => [
                '(c) 2022 Test Company(tm), (r) alle Rechte vorbehalten',
                '© 2022 Test Company™, ® alle Rechte <span class="long-word">vorbehalten</span>'
            ]
        ];
    }

    /**
     * @dataProvider texts
     */
    public function testTypography($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale);
        $typographer->setFlow('inline');

        $this->assertSame($expected, $typographer->parse($input)->toString());
    }
}
