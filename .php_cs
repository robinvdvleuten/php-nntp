<?php

use SLLH\StyleCIBridge\ConfigBridge;

require_once __DIR__.'/vendor/sllh/php-cs-fixer-styleci-bridge/autoload.php';

$config = ConfigBridge::create();

$header = <<<EOF
This file is part of the NNTP library.

(c) Robin van der Vleuten <robin@webstronauts.co>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return $config
    ->setRules(array_merge($config->getRules(), [
        'header_comment' => ['header' => $header]
    ]));
