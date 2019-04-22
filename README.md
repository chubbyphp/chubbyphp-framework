# chubbyphp-framework

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)

## Description

A minimal framework using [PHP Framework Interop Group - PSR][1]

 * [Basic Coding Standard (1)][2]
 * [Coding Style Guide (2)][3]
 * [Logger Interface (3)][4]
 * [Autoloading Standard (4)][5]
 * [HTTP Message Interface (7)][6]
 * [Container Interface (11)][7]
 * [HTTP Handlers (15)][8]
 * [HTTP Factories (17)][9]

The goal of this framework is to achive the best combination of flexibility and simplicity by using standards.

About flexibility: Everything should be replaceable the a framework user.
About simplicity: Nothing should be more complex than needed to fulfill the flexibility.

## Requirements

 * php: ^7.2
 * psr/container: ^1.0
 * psr/http-factory: ^1.0
 * psr/http-message-implementation: ^1.0
 * psr/http-message: ^1.0.1
 * psr/http-server-middleware: ^1.0.1
 * psr/log: ^1.1

## Suggest

 * aura/router: ^3.1
 * nikic/fast-route: ^1.3
 * zendframework/zend-diactoros: ^2.1.1

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework][20].

```sh
composer create-project chubbyphp/chubbyphp-framework -s dev example
cd example
```

## Usage

### Basic

[Aura.Router][30]
[FastRoute][31]

## Web Server

[Go to web server configuration][50]

## Copyright

Dominik Zogg 2019

[1]: https://www.php-fig.org/psr/

[2]: https://www.php-fig.org/psr/psr-1
[3]: https://www.php-fig.org/psr/psr-2
[4]: https://www.php-fig.org/psr/psr-3
[5]: https://www.php-fig.org/psr/psr-4
[6]: https://www.php-fig.org/psr/psr-7
[7]: https://www.php-fig.org/psr/psr-11
[8]: https://www.php-fig.org/psr/psr-15
[9]: https://www.php-fig.org/psr/psr-17

[20]: https://packagist.org/packages/chubbyphp/chubbyphp-framework

[30]: doc/usage/basic/aurarouter.md
[31]: doc/usage/basic/fastroute.md

[50]: doc/webserver.md
