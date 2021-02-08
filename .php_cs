<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return PhpCsFixer\Config::create()
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'dir_constant' => true,
        'final_class' => false,
        'is_null' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'method_chaining_indentation' => false,
        'no_php4_constructor' => true,
        'ordered_interfaces' => true,
        'php_unit_dedicate_assert_internal_type' => true,
        'php_unit_dedicate_assert' => true,
        'php_unit_expectation' => true,
        'php_unit_mock' => true,
        'php_unit_namespaced' => true,
        'php_unit_no_expectation_annotation' => true,
        'phpdoc_to_comment' => false,
        'single_line_throw' => false,
        'static_lambda' => true,
        'ternary_to_null_coalescing' => true,
        'use_arrow_functions' => false,
        'void_return' => true,
        'yoda_style' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
