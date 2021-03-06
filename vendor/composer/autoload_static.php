<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9e564d1b96cf01cc5fb1feae1cd06e7b
{
    public static $prefixLengthsPsr4 = array (
        'H' => 
        array (
            'Hananils\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Hananils\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Hananils\\Correction' => __DIR__ . '/../..' . '/lib/Correction.php',
        'Hananils\\Corrections\\Abbreviations' => __DIR__ . '/../..' . '/lib/Corrections/Abbreviations.php',
        'Hananils\\Corrections\\Apostrophes' => __DIR__ . '/../..' . '/lib/Corrections/Apostrophes.php',
        'Hananils\\Corrections\\Caps' => __DIR__ . '/../..' . '/lib/Corrections/Caps.php',
        'Hananils\\Corrections\\Dashes' => __DIR__ . '/../..' . '/lib/Corrections/Dashes.php',
        'Hananils\\Corrections\\Ellipsis' => __DIR__ . '/../..' . '/lib/Corrections/Ellipsis.php',
        'Hananils\\Corrections\\LongWords' => __DIR__ . '/../..' . '/lib/Corrections/LongWords.php',
        'Hananils\\Corrections\\Math' => __DIR__ . '/../..' . '/lib/Corrections/Math.php',
        'Hananils\\Corrections\\Primes' => __DIR__ . '/../..' . '/lib/Corrections/Primes.php',
        'Hananils\\Corrections\\Punctation' => __DIR__ . '/../..' . '/lib/Corrections/Punctation.php',
        'Hananils\\Corrections\\Quotes' => __DIR__ . '/../..' . '/lib/Corrections/Quotes.php',
        'Hananils\\Corrections\\Trademarks' => __DIR__ . '/../..' . '/lib/Corrections/Trademarks.php',
        'Hananils\\Corrections\\Widont' => __DIR__ . '/../..' . '/lib/Corrections/Widont.php',
        'Hananils\\Document' => __DIR__ . '/../..' . '/lib/Document.php',
        'Hananils\\Locale' => __DIR__ . '/../..' . '/lib/Locale.php',
        'Hananils\\Typographer' => __DIR__ . '/../..' . '/lib/Typographer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9e564d1b96cf01cc5fb1feae1cd06e7b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9e564d1b96cf01cc5fb1feae1cd06e7b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9e564d1b96cf01cc5fb1feae1cd06e7b::$classMap;

        }, null, ClassLoader::class);
    }
}
