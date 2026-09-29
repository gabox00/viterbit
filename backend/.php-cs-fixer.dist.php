<?php

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests', __DIR__.'/migrations', __DIR__.'/config'])
    ->notPath(['bundles.php', 'reference.php', 'preload.php']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2x0' => true,
        '@Symfony' => true,
        'declare_strict_types' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'no_unused_imports' => true,
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder);
