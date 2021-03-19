# API Server Sample

Just a little API server project allowing to manage a catalog of pizzas and their ingredients.

## Requirements

- PHP 8.0.0 or higher
- MySQL 8.0 or higher
- Composer 2
- `symfony` binary

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
