# API Server Sample

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Bouss/api-server-sample/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Bouss/api-server-sample/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/Bouss/api-server-sample/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Bouss/api-server-sample/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/Bouss/api-server-sample/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Bouss/api-server-sample/build-status/master) [![Code Intelligence Status](https://scrutinizer-ci.com/g/Bouss/api-server-sample/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Just a little API server project allowing to manage a catalog of pizzas and their ingredients

![image](https://user-images.githubusercontent.com/14886236/111867529-e79d7600-8974-11eb-9518-90369c97b5f8.png)

## Requirements

- PHP 8.0.0 or higher
- MySQL 8.0 or higher
- Composer 2
- `symfony` binary (download [here](https://symfony.com/download))

## Configuration

- Create your own `.env.local` file from the `.env` file
- In the `.env.local` file, replace the value of the `DATABASE_URL` env var with your own value

## Installation

```
$ composer install
$ bin/console doctrine:migration:migrate
$ bin/console doctrine:fixtures:load
```

## Usage

```
$ symfony serve
```

### Sandbox

The API can be tested using the sandbox: https://localhost:8000/api/doc (by default)

### Authentication

A header `X-API-KEY` must be provided for each API call. Your API key is: `mustBeASecret`

## Tests

```
$ ./bin/phpunit
```
