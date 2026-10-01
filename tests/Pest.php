<?php

$base = realpath(__DIR__ . '/../../../../');

require_once $base . '/kirby/bootstrap.php';

load([
    'Hananils\\Plus\\Discovery' => '../../../../hananils/Discovery.php',
    'Hananils\\Plus\\License' => '../../../../hananils/License.php',
    'Hananils\\Plus\\LicenseManager' => '../../../../hananils/LicenseManager.php',
    'Hananils\\Plus\\PlusPlugin' => '../../../../hananils/PlusPlugin.php',
    'Hananils\\Plus\\Status' => '../../../../hananils/Status.php',
    'Hananils\\Plus\\Utilities' => '../../../../hananils/Utilities.php'
], __DIR__);

$kirby = new Kirby([
    'roots' => [
        'index' => __DIR__,
        'base' => $base,
        'content' => $base . '/content',
        'site' => $base . '/site',
        'storage' => $storage = $base . '/storage',
        'accounts' => $storage . '/accounts',
        'cache' => $storage . '/cache',
        'sessions' => $storage . '/sessions',
        'logs' => $storage . '/logs',
        'licenses' => $storage . '/licenses',
        'license' => $storage . '/licenses/.license'
    ]
]);

