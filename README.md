# NNTP

Client for communicating with servers throught the Network News Transfer Protocol (NNTP) protocol.

[![Build Status](https://travis-ci.org/RobinvdVleuten/php-nntp.png?branch=master)](https://travis-ci.org/RobinvdVleuten/php-nntp)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/badges/quality-score.png?s=f75dede4b0dfd176b1448b72e9acc8345f132a52)](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/)
[![Code Coverage](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/badges/coverage.png?s=e60c63bee8c99a655f821051fee3b7be45ffbb3c)](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/115c5524-7c3a-4463-a48c-2e21257f25b4/mini.png)](https://insight.sensiolabs.com/projects/115c5524-7c3a-4463-a48c-2e21257f25b4)

## Installation

The recommended way to install the library is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "rvdv/nntp": "0.1.*@dev"
    }
}
```

## NNTP?

> The Network News Transfer Protocol (NNTP) has been in use in the Internet for a decade, and remains one of the most popular protocols (by volume) in use today.  This document is a replacement for RFC 977, and officially updates the protocol specification.  It clarifies some vagueness in RFC 977, includes some new base functionality, and provides a specific mechanism to add standardized extensions to NNTP.

> -- RFC Abstract ([source](http://tools.ietf.org/html/rfc3977))

## Usage

```php
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

// Send the QUIT command first before disconnecting.
$client->quit();

// Disconnect the established socket connection.
$client->disconnect();
```

## License

MIT, see LICENSE
