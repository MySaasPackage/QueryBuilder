<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'strict_comparison' => true,
        'array_syntax' => ['syntax' => 'short'],
        'array_indentation' => true,
        'multiline_whitespace_before_semicolons' => true,
        'blank_line_after_opening_tag' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'concat_space' => ['spacing' => 'one'],
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'ordered_imports' => ['sort_algorithm' => 'length', 'imports_order' => ['const', 'class', 'function']],
        'single_blank_line_at_eof' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true);
