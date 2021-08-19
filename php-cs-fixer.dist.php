<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app/')
    ->name('*.php');

$config = new PhpCsFixer\Config();
$config->setRules([
        '@PSR1' => true,
        '@PSR12' => true,
    ])
    ->setFinder($finder);
return $config;
