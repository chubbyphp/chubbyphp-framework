<?php

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
        'final_class' => true,
        'is_null' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => true,
        'method_chaining_indentation' => false,
        'no_php4_constructor' => true,
        'ordered_interfaces' => true,
        'php_unit_dedicate_assert_internal_type' => true,
        'php_unit_dedicate_assert' => true,
        'php_unit_expectation' => true,
        'php_unit_mock' => true,
        'php_unit_namespaced' => true,
        'php_unit_no_expectation_annotation' => true,
        'single_line_throw' => false,
        'ternary_to_null_coalescing' => true,
        'void_return' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
