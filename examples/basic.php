<?php

require_once __DIR__.'/../vendor/autoload.php';

use Rvdv\Nntp\Client;

$client = Client::create();
$response = $client->connect('news-europe.giganews.com', 443, true);
var_dump($response);
