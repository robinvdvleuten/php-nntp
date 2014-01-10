<?php

require_once __DIR__.'/../vendor/autoload.php';

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

$client = new Client();

$connection = new Connection('news.php.net', 119);
$client->setConnection($connection);

$client->connect();

$command = $client->overviewFormat();
$overviewFormat = $command->getResult();

$command = $client->group('php.doc');
$group = $command->getResult();

$command = $client->overview($group['first'], $group['first'] + 100, $overviewFormat);
$articles = $command->getResult();

// Process the articles further...

$client->disconnect();
