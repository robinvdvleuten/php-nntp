<?php

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

require_once dirname(__DIR__).'/vendor/autoload.php';

$connection = new Connection('news.php.net', 119);
$client = new Client($connection);

$client->connect();

$command = $client->overviewFormat();
$overviewFormat = $command->getResult();

$command = $client->group('php.doc');
$group = $command->getResult();

$command = $client->xover($group['first'], $group['first'] + 10, $overviewFormat);
$articles = $command->getResult();

var_dump($articles);

$client->disconnect();
