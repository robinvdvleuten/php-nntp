<?php

$header = <<<EOF
This file is part of the NNTP library.

(c) Robin van der Vleuten <robin@webstronauts.co>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder);
