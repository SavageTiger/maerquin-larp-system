<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/app')
;

return (new Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'method_chaining_indentation' => true,
        'global_namespace_import' => ['import_classes' => true],
        'single_line_empty_body' => false,
        'return_type_declaration' => ['space_before' => 'one'],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'php_unit_test_class_requires_covers' => false,
        'php_unit_internal_class' => false,
        'operator_linebreak' => ['position' => 'beginning'],
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder);
