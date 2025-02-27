<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'yoda_style' => false,
        'final_class' => true,
        'return_to_yield_from' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
