<?php

require_once __DIR__.'/../vendor/autoload.php';

use Rvdv\Nntp\Client;

$client = Client::create();
$client->connect('news.php.net', 119);
$client->authenticate('username', 'password');

$client->disconnect();
