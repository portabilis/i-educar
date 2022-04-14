<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        'app',
        'config',
        'database',
        'routes',
        'src',
        'tests',
    ]);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12' => true,
    'phpdoc_align' => [
        'align' => 'vertical'
    ],
    'single_quote' => [
        'strings_containing_single_quote_chars' => true
    ],
    'phpdoc_separation' => true,
    'blank_line_before_statement' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_extra_blank_lines' => true,
    'no_unused_imports' => true,
    'ordered_imports' => [
        'sort_algorithm' => 'alpha'
    ]
])->setFinder($finder);
