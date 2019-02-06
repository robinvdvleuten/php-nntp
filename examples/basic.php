<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

require_once dirname(__DIR__).'/vendor/autoload.php';

$connection = new Connection('news.php.net', 119);
$client = new Client($connection);

$client->connect();

$overviewFormat = $client->overviewFormat();
$group = $client->group('php.doc');
$articles = $client->xover($group['first'], $group['first'] + 10, $overviewFormat);

var_dump($articles);

$client->disconnect();
