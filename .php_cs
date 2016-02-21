<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;
use Symfony\CS\FixerInterface;

$header = <<<EOF
This file is part of the NNTP library.

(c) Robin van der Vleuten <robinvdvleuten@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

return Config::create()
    ->level(FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-psr0',
        '-empty_return',
        'align_double_arrow',
        'ordered_use',
        'phpdoc_order',
        'short_array_syntax',
    ])
    ->finder(
        DefaultFinder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    );
