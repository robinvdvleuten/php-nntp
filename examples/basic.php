<?php

require_once __DIR__.'/../vendor/autoload.php';

use Rvdv\Nntp\Client;

$client = Client::create();
$response = $client->connect('news.php.net', 119);
var_dump($response);
