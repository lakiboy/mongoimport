# mongoimport

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
