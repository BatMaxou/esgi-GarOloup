<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('var')
    ->in('./api/src')
    ->in('./api/tests')
    ->in('./api/fixtures')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PHP84Migration' => true,
        '@DoctrineAnnotation' => true,
    ])
    ->setFinder($finder)
;
