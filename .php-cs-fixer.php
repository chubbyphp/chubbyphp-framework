<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

/** @var array $config */
$config = require __DIR__ . '/vendor/chubbyphp/chubbyphp-dev-helper/phpcs.php';

unset($config['rules']['final_class']);

$config['rules']['native_function_invocation'] = false;
$config['rules']['phpdoc_to_param_type'] = ['scalar_types' => false];
$config['rules']['phpdoc_to_return_type'] = false;

return (new PhpCsFixer\Config)
    ->setIndent($config['indent'])
    ->setLineEnding($config['lineEnding'])
    ->setRules($config['rules'])
    ->setRiskyAllowed($config['riskyAllowed'])
    ->setFinder($finder)
;
