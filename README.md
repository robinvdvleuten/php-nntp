# NNTP

Client for communicating with servers throught the Network News Transfer Protocol (NNTP) protocol.

[![Latest Version](http://img.shields.io/packagist/v/rvdv/nntp.svg?style=flat)](https://packagist.org/packages/rvdv/nntp)
[![Build Status](http://img.shields.io/travis/robinvdvleuten/php-nntp.svg?style=flat)](https://travis-ci.org/robinvdvleuten/php-nntp)
[![Build status](https://img.shields.io/appveyor/ci/robinvdvleuten/php-nntp.svg?style=flat)](https://ci.appveyor.com/project/robinvdvleuten/php-nntp)
[![Scrutinizer Quality Score](http://img.shields.io/scrutinizer/g/robinvdvleuten/php-nntp.svg?style=flat)](https://scrutinizer-ci.com/g/robinvdvleuten/php-nntp/)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/robinvdvleuten/php-nntp.svg?style=flat)](https://scrutinizer-ci.com/g/robinvdvleuten/php-nntp/)
[![Gittip](http://img.shields.io/gittip/robinvdvleuten.svg?style=flat)](https://www.gittip.com/robinvdvleuten/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/115c5524-7c3a-4463-a48c-2e21257f25b4/mini.png)](https://insight.sensiolabs.com/projects/115c5524-7c3a-4463-a48c-2e21257f25b4)

## Installation

The recommended way to install the library is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "rvdv/nntp": "~0.6"
    }
}
```

## NNTP?

> NNTP specifies a protocol for the distribution, inquiry, retrieval,
> and posting of news articles using a reliable stream (such as TCP)
> server-client model. NNTP is designed so that news articles need only
> be stored on one (presumably central) host, and subscribers on other
> hosts attached to the LAN may read news articles using stream
> connections to the news host.

> -- RFC Abstract ([source](http://tools.ietf.org/html/rfc977))

## Usage

Here is an example that fetches 100 articles from the _php.doc_ of the _news.php.net_ server:

```php
<?php

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

$connection = new Connection('news.php.net', 119);
$client = new Client($connection);

$client->connect();

$command = $client->overviewFormat();
$overviewFormat = $command->getResult();

$command = $client->group('php.doc');
$group = $command->getResult();

$command = $client->xover($group['first'], $group['first'] + 100, $overviewFormat);
$articles = $command->getResult();

// Process the articles further...

$client->disconnect();
```

## Tests

To run the test suite, you need install the dependencies via composer, then run PHPUnit.

```bash
$ composer install
$ php vendor/bin/phpunit
```

## License

MIT, see LICENSE
