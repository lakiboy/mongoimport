# mongoimport

[![Build Status](https://travis-ci.org/dev-machine/mongoimport.svg)](https://travis-ci.org/dev-machine/mongoimport) [![Coverage Status](https://coveralls.io/repos/dev-machine/mongoimport/badge.svg?branch=master&service=github)](https://coveralls.io/github/dev-machine/mongoimport?branch=master)

PHP implementation of [mongoimport](https://docs.mongodb.org/manual/reference/program/mongoimport/).

## About

Why would you need a custom _mongoimport_ instead of default utility supplied with mongo? In certain setup (read Docker) mongo client is not available. With _mongo_ extension enabled in PHP, you can import JSON created by mongoexport with this tiny library.

## Installation 

Add the following to your composer.json:

```javascript
{
    "require": {
        "devmachine/mongoimport": "~1.0"
    }
}
```

## Usage

Import _movies.json_ into _cinema_ database. Basename of filename without extension was used as a collection name.

```bash
$ ./bin/mongoimport movies.json --db hollywood
```

By default utility connects to mongod running on `localhost:_27017_`. In docker environment default host is `MONGO_PORT_27017_TCP_ADDR` and default port is `MONGO_PORT_27017_TCP_PORT`.

Specifying custom host, port and collection name:

```bash
$ ./bin/mongoimport movies.json -c shows --db hollywood --host <host> -p <port>
```

To drop existing collection prior to import, use `--drop` flag.

For more info use:

```bash
$ ./bin/mongoimport -h
```

## Running in Docker

##### Init

```bash
$ docker-composer up -d # puts mongo container in bg
```

##### Load fixtures

```bash
$ docker-compose run --entrypoint php composer fixtures/load.php
```

##### Exporting data

From container:

```bash
$ mongoexport --db company --collection employees --jsonArray --pretty --out employees.json
$ mongoexport --db company --collection offices --jsonArray --pretty --out offices.json
```

From host:

```bash
$ docker cp mongoimport_mongo_1:/employees.json ./tests/fixtures/
$ docker cp mongoimport_mongo_1:/offices.json ./tests/fixtures/
```

##### Running tests

```bash
$ docker-compose run --entrypoint php composer bin/phpunit
```

##### Running utility 

```bash
$ docker-compose run --entrypoint php composer bin/mongoimport -V
```
