<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/example')
    ->files()
    ->name('*.php');

// https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16
// https://github.com/mlocati/php-cs-fixer-configurator
$config = new PhpCsFixer\Config();
$config->setRules([
    // generic PSRs
    '@PSR1' => true,
    '@PSR2' => true,
    'psr_autoloading' => true,

    // imports
    'ordered_imports' => true,
    'no_unused_imports' => true,
    'php_unit_namespaced' => ['target' => '6.0'],
    'php_unit_expectation' => true,

    'phpdoc_align' => ['align' => 'left'],
    'array_syntax' => ['syntax' => 'short'],

    // Otherwise anonymous classes cannot be used with any meaningful constructor arguments.
    // It is temporary for now, until a decision is made by PSR-12 editors (see
    // https://github.com/php-fig/fig-standards/pull/1206#issuecomment-628873709 ) and
    // and a PR to php-cs-fixer will be proposed based on that decision to address it one way or the other.
    'class_definition' => false,
])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
//    ->registerCustomFixers([new Drew\DebugStatementsFixers\Dump()])
    ->setCacheFile(__DIR__ . DIRECTORY_SEPARATOR . '.php-cs-fixer.' . PHP_VERSION . '.cache');

return $config;
