[![CI](https://github.com/axel37/supinfo-4cite/actions/workflows/ci.yml/badge.svg)](https://github.com/axel37/supinfo-4cite/actions/workflows/ci.yml)

# Hotel API

## Quick install guide

This application makes use of containers through Docker and Docker compose which provide an easy-to-run development environment. Docker compose simulates a production environment by creating containers for all the needed services and exposing them in a virtual network.

## Pre-requisites

- Have `docker` and `docker compose` installed on your machine. An easy way to do that is by installing [Docker Desktop](https://www.docker.com/products/docker-desktop/) (though be mindful of its licencing requirements).

## Running the application

- `docker compose up --wait` : Run the application (and build it on first run)
- `docker compose exec php bin/console d:d:c` : Create the database
- `docker compose exec php bin/console d:m:m` : Create / update the database structure
- `docker exec php bin/console lexik:jwt:generate-keypair` : Create keys for JWT generation

After these steps, the API should now be available on [localhost/docs](https://localhost/docs), while the front-end application is located at [localhost/admin](https://localhost/admin).

See the next sections for more commands and troubleshooting steps.

## Troubleshooting

If the `php` container does not start (or is "Unhealthy") during `docker compose up`, try running `./update-deps.sh`.

If error messages relating to the database appear, try running `docker compose exec php bin/console d:m:m` to update the database schema.

In other cases, restarting the containers can help : `docker compose down`.

## Running tests

To run the test suite, run :

`docker compose exec php bin/phpunit`

Before running tests for the first time, you will need to initialize a database for the `TEST` environment, like so :

- `docker compose exec php bin/console -e test d:d:c`
- `docker compose exec php bin/console -e test d:m:m`

## Useful commands

`docker compose build --no-cache` : Build images

`docker compose up --wait` : Start services

`docker compose logs -f` : See logs

`docker compose ps` : See containers

`docker compose exec php bin/phpunit` : Run tests

`docker compose exec php bin/console -e test ...` : Execute commands for the test environment (create db, migrate...)

`XDEBUG_MODE=debug docker compose up --wait` : Enable XDebug

`docker exec php bin/console lexik:jwt:generate-keypair` : Generate keys for JWT authentication
