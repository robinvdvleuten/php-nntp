<?php

require_once __DIR__.'/../vendor/autoload.php';

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

$client = new Client();

$connection = new Connection();
$client->setConnection($connection);

$client->connect('news.php.net', 119);
$client->authenticate('username', 'password');

$command = $client->overviewFormat();
$overviewFormat = $command->getResult();

$command = $client->group('alt.binaries.moovee');
$group = $command->getResult();

$command = $client->overview($group['first'], $group['first'] + 100, $overviewFormat);
$articles = $command->getResult();

var_dump(count($articles));

$client->disconnect();
