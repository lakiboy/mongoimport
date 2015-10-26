# mongoimport

## Running in Docker

##### Init

```bash
$ docker-composer up -d # puts mongo container on bg
```

##### Load fixtures

```bash
$ docker-compose run --entrypoint php composer fixtures/load.php
```

##### Exporting data

```bash
$ mongoexport --db company --collection employees --jsonArray --pretty --out employees.json
$ mongoexport --db company --collection offices --jsonArray --pretty --out offices.json
```

##### Running tests

```bash
$ docker-compose run --entrypoint bin/phpunit composer --exclude-group integration
```
