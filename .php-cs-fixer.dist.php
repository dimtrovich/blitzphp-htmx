<?php

/**
 * This file is part of dimtrovich/blitzphp-htmx.
 *
 * (c) 2025 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BlitzPHP\CodingStandard\Blitz;
use Nexus\CsConfig\Factory;
use Nexus\CsConfig\Fixer\Comment\NoCodeSeparatorCommentFixer;
use Nexus\CsConfig\FixerGenerator;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/spec',
    ])
    ->notName('#Foobar.php$#')
    ->append([
        __FILE__,
    ]);

$overrides = [
    'static_lambda' => false,
];

$options = [
    'cacheFile'    => 'build/.php-cs-fixer.cache',
    'finder'       => $finder,
    'customFixers' => FixerGenerator::create('vendor/nexusphp/cs-config/src/Fixer', 'Nexus\\CsConfig\\Fixer'),
    'customRules'  => [
        NoCodeSeparatorCommentFixer::name() => true,
    ],
];

return Factory::create(new Blitz(), $overrides, $options)->forLibrary(
    'dimtrovich/blitzphp-htmx',
    'Dimitri Sitchet Tomkeu',
    'devcode.dst@gmail.com',
    date('Y')
);
