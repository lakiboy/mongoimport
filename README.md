# mongoimport

[![Build Status](https://travis-ci.org/dev-machine/mongoimport.svg)](https://travis-ci.org/dev-machine/mongoimport) [![Coverage Status](https://coveralls.io/repos/dev-machine/mongoimport/badge.svg?branch=master&service=github)](https://coveralls.io/github/dev-machine/mongoimport?branch=master)

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
$ docker-compose run --entrypoint bin/phpunit composer --exclude-group integration
```
