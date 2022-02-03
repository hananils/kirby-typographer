<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Typographer;

class TypographerBlockTest extends PHPUnit\Framework\TestCase
{
    public function texts()
    {
        return [
            'simple sentence with German locale' => [
                '<p>Dies ist ein einfacher Satz.</p>',
                '<p>Dies ist ein einfacher Satz.</p>',
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
                '<p>"Wo ist hier \'Nils\' Zitatende\'?"</p>',
                '<p>„Wo ist hier ‚Nils’ Zitatende‘?“</p>',
                'de-DE'
            ],
            'primary and secondary quotes with English locale' => [
                '<p>"Wo ist hier \'Nils\' Zitatende\'?"</p>',
                '<p>“Wo ist hier ‘Nils’ Zitatende’?”</p>',
                'en-US'
            ],
            'primary and secondary quotes with French locale' => [
                '<p>"Wo ist hier \'Nils\' Zitatende\'?"</p>',
                '<p>« Wo ist hier ‹ Nils › Zitatende’? »</p>',
                'fr-FR'
            ],

            /**
             * Wörtliche Rede, siehe https://de.wikipedia.org/wiki/Direkte_Rede (CC BY-SA 3.0)
             */
            'direct speech (multiple paragraphs) with German locale' => [
                '<p>Er rief: "Guck mal, es schneit!"</p><p>Sie sagte: "Guck mal, es schneit!"</p><p>Er meinte: "Es schneit."</p><p>"Schau mal, es schneit!", rief er.</p><p>"Schau mal, es schneit!", sagte sie.</p><p>"Es schneit!", meinte er.</p><p>"Zieh deine Winterjacke an", mahnte der Vater, "sonst wird dir kalt."</p><p>Sie sagte: "Es schneit und mir ist kalt." – "Hast du deine Winterjacke angezogen?", fragte er.</p>',
                '<p>Er rief: „Guck mal, es schneit!“</p><p>Sie sagte: „Guck mal, es schneit!“</p><p>Er meinte: „Es schneit.“</p><p>„Schau mal, es schneit!“, rief er.</p><p>„Schau mal, es schneit!“, sagte sie.</p><p>„Es schneit!“, meinte er.</p><p>„Zieh deine <span class="long-word">Winterjacke</span> an“, mahnte der Vater, „sonst wird dir kalt.“</p><p>Sie sagte: „Es schneit und mir ist kalt.“ – „Hast du deine <span class="long-word">Winterjacke</span> angezogen?“, fragte er.</p>',
                'de-DE'
            ],
            'direct speech (inset) with German locale' => [
                '<p>"Da vorne", sagt Lello, "ist ja schon die Villa."</p>',
                '<p>„Da vorne“, sagt Lello, „ist ja schon die Villa.“</p>',
                'de-DE'
            ],
            'direct speech (two sentences) with German locale' => [
                '<p>"Ich weiß noch, wie du Nala bekommen hast", sagt Lilli. "Du hast dich damals riesig gefreut."</p>',
                '<p>„Ich weiß noch, wie du Nala bekommen hast“, sagt Lilli. „Du hast dich damals riesig gefreut.“</p>',
                'de-DE'
            ],
            'direct speech (question + sentence) with German locale' => [
                '<p>"Weißt du noch, wie du Nala bekommen hast?", fragt Lilli. "Du hast dich damals riesig gefreut."</p>',
                '<p>„Weißt du noch, wie du Nala bekommen hast?“, fragt Lilli. „Du hast dich damals riesig gefreut.“</p>',
                'de-DE'
            ],

            /**
             * Examples with contextual locale where the output should be the same
             * independent of the global locale.
             */
            'contextual locale with English overriding German locale 1' => [
                '<p lang="en">"We are at 118° 19\' 43.5"", said the captain while pointing on the map.</p>',
                '<p lang="en">“We are at 118° 19′ 43.5″”, said the captain while pointing on the map.</p>',
                'de-DE'
            ],
            'contextual locale with English overriding French locale 1' => [
                '<p lang="en">"We are at 118° 19\' 43.5"", said the captain while pointing on the map.</p>',
                '<p lang="en">“We are at 118° 19′ 43.5″”, said the captain while pointing on the map.</p>',
                'fr-FR'
            ],

            'contextual locale with German overriding English locale' => [
                '<p lang="de">"Wir brauchen auch \'Zitate in Zitaten\' - nicht, dass das untergeht!" ergänzte der Grafiker.</p>',
                '<p lang="de">„Wir brauchen auch ‚Zitate in Zitaten‘ – nicht, dass das untergeht!“ ergänzte der Grafiker.</p>',
                'en-GB'
            ],
            'contextual locale with German overriding French locale' => [
                '<p lang="de">"Wir brauchen auch \'Zitate in Zitaten\' - nicht, dass das untergeht!" ergänzte der Grafiker.</p>',
                '<p lang="de">„Wir brauchen auch ‚Zitate in Zitaten‘ – nicht, dass das untergeht!“ ergänzte der Grafiker.</p>',
                'fr-FR'
            ],

            'contextual locale with English overriding German locale 2' => [
                '<p lang="en">The programmer added: "Can we please make sure that my code stays intact?" Of course, we can: <code>let test = "This is text in straight quotes with ... dots"</code>.</p>',
                '<p lang="en">The <span class="long-word">programmer</span> added: “Can we please make sure that my code stays intact?” Of course, we can: <code>let test = "This is text in straight quotes with ... dots"</code>.</p>',
                'de-DE'
            ],
            'contextual locale with English overriding French locale 2' => [
                '<p lang="en">The programmer added: "Can we please make sure that my code stays intact?" Of course, we can: <code>let test = "This is text in straight quotes with ... dots"</code>.</p>',
                '<p lang="en">The <span class="long-word">programmer</span> added: “Can we please make sure that my code stays intact?” Of course, we can: <code>let test = "This is text in straight quotes with ... dots"</code>.</p>',
                'fr-FR'
            ],

            /**
             * Inline apostrophes
             */
            'inline apostrophs with German locale' => [
                '<p>Rock\'n\'Roll, Grimm\'sche Märchen, "Kennt Ihr die \'Grimm\'schen Märchen\'?"</p>',
                '<p>Rock’n’Roll, Grimm’sche Märchen, „Kennt Ihr die ‚Grimm’schen Märchen‘?“</p>',
                'de-DE'
            ],
            'inline apostrophs with French locale' => [
                '<p>Rock\'n\'Roll, Grimm\'sche Märchen, "Kennt Ihr die \'Grimm\'schen Märchen\'?"</p>',
                '<p>Rock’n’Roll, Grimm’sche Märchen, « Kennt Ihr die ‹ Grimm’schen Märchen ›? »</p>',
                'fr-FR'
            ],

            /**
             * Abbreviations with apostrophs
             */
            'opening apostroph with German locale' => [
                '<p>Was für \'n Blödsinn! Kommen S\' nur herein!</p>',
                '<p>Was für ’n Blödsinn! Kommen S’ nur herein!</p>',
                'de_DE'
            ],

            /**
             * Math
             */
            'times signs' => [
                '<p>Ein DIN-A4-Blatt ist 21 x 29,7 cm groß.</p>',
                '<p>Ein <abbr>DIN</abbr>-A4-Blatt ist 21 × 29,7 cm groß.</p>'
            ],

            /**
             * Trademarks
             */
            'trademarks' => [
                '<p>(c) 2022 Test Company(tm), (r) alle Rechte vorbehalten</p>',
                '<p>© 2022 Test Company™, ® alle Rechte <span class="long-word">vorbehalten</span></p>'
            ],

            /**
             * French punctation
             */
            'french punctation' => [
                '<p>"Ceci est une citation : il y a quelques signes de ponctuation !"</p>',
                '<p>« Ceci est une citation : il y a quelques signes de <span class="long-word">ponctuation</span> ! »</p>',
                'fr_FR'
            ]
        ];
    }

    /**
     * @dataProvider texts
     */
    public function testTypography($input, $expected, $locale = 'en-US')
    {
        $typographer = new Typographer($locale);
        $typographer->setFlow('block');

        $this->assertSame($expected, $typographer->parse($input)->toString());
    }
}
