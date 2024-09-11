<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
    ->in(__DIR__);

return (new Config())
    ->setRules([
        '@PhpCsFixer'      => true,
        '@PHP82Migration'  => true,
        'indentation_type' => true,

        // Overrides for (opinionated) @PhpCsFixer and @Symfony rules:

        // Align "=>" in multi-line array definitions, unless a blank line exists between elements
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],

        // Subset of statements that should be proceeded with blank line
        'blank_line_before_statement' => [
            'statements' => [
                'case',
                'continue',
                'declare',
                'default',
                'return',
                'throw',
                'try',
                'yield',
                'yield_from',
            ],
        ],

        // Enforce space around concatenation operator
        'concat_space' => [
            'spacing' => 'one',
        ],

        // Use {} for empty loop bodies
        'empty_loop_body' => [
            'style' => 'braces',
        ],

        // Don't change any increment/decrement styles
        'increment_style' => false,

        // Forbid multi-line whitespace before the closing semicolon
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],

        // Clean up PHPDocs, but leave @inheritDoc entries alone
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed'       => true,
            'remove_inheritdoc' => false,
        ],

        // Ensure that traits, constants, properties, and the constructor
        // are listed first in classes, and magic properties are at the end
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
            ],
        ],

        // Ensure that param and return types are sorted consistently, with null at end
        'phpdoc_types_order' => [
            'sort_algorithm'  => 'alpha',
            'null_adjustment' => 'always_last',
        ],

        // Yoda style is too weird
        'yoda_style' => false,
    ])
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder);
