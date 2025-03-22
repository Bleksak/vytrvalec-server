<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRules([
        '@Symfony' => true,
        'yoda_style' => false,
        'final_class' => true,
        'return_to_yield_from' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        PhpCsFixerCustomFixers\Fixer\NoDoctrineMigrationsGeneratedCommentFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer::name() => [
            'keep_blank_lines' => true,
        ],
        PhpCsFixerCustomFixers\Fixer\StringableInterfaceFixer::name() => true,
        'method_chaining_indentation' => true,
        'declare_strict_types' => true,
    ])
    ->setLineEnding("\n")
    ->setIndent('    ')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
