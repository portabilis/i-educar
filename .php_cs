<?php


$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->name('*.phpt')
    ->in('ieducar');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        // All items of the @param, @throws, @return, @var, and @type phpdoc
        // tags must be aligned vertically.
        'phpdoc_align' => [
            'align' => 'vertical'
        ],
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => [
            'strings_containing_single_quote_chars' => true
        ],
        // Group and seperate @phpdocs with empty lines.
        'phpdoc_separation' => true,
        // An empty line feed should precede a return statement.
        'blank_line_before_return' => true,
        // PHP arrays should use the PHP 5.4 short-syntax.
        'array_syntax' => ['syntax' => 'short'],
        // Remove trailing whitespace at the end of blank lines.
        'no_whitespace_in_blank_line' => true,
        // Removes extra empty lines.
        'no_extra_consecutive_blank_lines' => true,
        // Unused use statements must be removed.
        'no_unused_imports' => true,
        // Ordering use statements.
        'ordered_imports' => [
            'sort_algorithm' => 'alpha'
        ]
    ])
    ->setFinder($finder);
