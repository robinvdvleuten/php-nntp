<?php

$header = <<<EOF
This file is part of the NNTP library.

(c) Robin van der Vleuten <robin@webstronauts.co>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PSR12' => true,
        'header_comment' => ['header' => $header]
    ])
    ->setFinder($finder);
