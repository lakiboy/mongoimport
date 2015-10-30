# mongoimport

[![Build Status](https://travis-ci.org/dev-machine/mongoimport.svg)](https://travis-ci.org/dev-machine/mongoimport) [![Coverage Status](https://coveralls.io/repos/dev-machine/mongoimport/badge.svg?branch=master&service=github)](https://coveralls.io/github/dev-machine/mongoimport?branch=master)

PHP implementation of [mongoimport](https://docs.mongodb.org/manual/reference/program/mongoimport/).

## About

Why would you need a custom _mongoimport_ instead of default utility supplied with mongo? In certain setup (read Docker) mongo client is not available. With _mongo_ extension enabled in PHP, you can import JSON created by mongoexport with this tiny library.

Provides integration with [Symfony](http://symfony.com/) (read below), therefore could be used as fixtures loader.

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

Import _movies.json_ into _hollywood_ database. Collection name is figured out automatically by using file's basename without extension.

```bash
$ ./bin/mongoimport movies.json --db hollywood
```

By default utility connects to mongod running on `localhost:27017`. In docker environment default host is `MONGO_PORT_27017_TCP_ADDR` and default port is `MONGO_PORT_27017_TCP_PORT`.

Overwriting default host, port and collection name:

```bash
$ ./bin/mongoimport movies.json -c shows --db hollywood --host <host> -p <port>
```

To drop existing collection prior to import, use `--drop` flag.

More info:

```bash
$ ./bin/mongoimport -h
```

## Symfony integration

Register bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        
        new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
        new Devmachine\MongoImport\Bundle\DevmachineMongoImportBundle(),
    );
}
```

When _DoctrineMongoDBBundle_ is enabled it registers the importer in container for each _ODM_ manager e.g.

 - `devmachine_mongoimport.default` (for _default_ manager)
 - `devmachine_mongoimport.secondary` (for _secondary_ manager)
 - `devmachine_mongoimport` alias for _default_ importer.
 
Example:

```php
// Import movies.json into "movies" collection in default database.
$total = $this
    ->get('devmachine_mongoimport')
    ->importCollection('movies.json')
;

// Drop existing collection prior to import.
$total = $this
    ->get('devmachine_mongoimport')
    ->import('movies.json', ['drop' => true])
;

// With specified collection name.
$total = $this
    ->get('devmachine_mongoimport')
    ->import('movies.json', 'films', ['drop' => true])
;

// With specified collection and db.
$total = $this
    ->get('devmachine_mongoimport')
    ->import('movies.json', 'films', 'hollywood', ['drop' => true])
;
```

## Contributing

Find below various docker commands.

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
