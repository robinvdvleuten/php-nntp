<?php

require_once __DIR__.'/../vendor/autoload.php';

$start = microtime(true);

use Rvdv\Nntp\Command\AuthInfoCommand;
use Rvdv\Nntp\Command\XFeatureCommand;
use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

$connection = new Connection('news-europe.giganews.com', 563, true);
$client = new Client($connection);

$client->connect();

$command = $client->authInfo(AuthInfoCommand::AUTHINFO_USER, 'robinvdvleuten');
$command = $client->authInfo(AuthInfoCommand::AUTHINFO_PASS, 'E9iKQdBWn9QgNe');

// $command = $client->xfeature(XFeatureCommand::COMPRESS_GZIP);
// var_dump($command->getResponse());

$command = $client->overviewFormat();
$overviewFormat = $command->getResult();

$command = $client->group('php.doc');
$group = $command->getResult();

$command = $client->overview($group['first'], $group['first'] + 1000, $overviewFormat);
$articles = $command->getResult();

// Process the articles further...
var_dump(count($articles));

$client->disconnect();

var_dump(sprintf('Code ran in %d seconds', microtime(true) - $start));
