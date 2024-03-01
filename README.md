`docker compose build --no-cache` : Build images

`docker compose up --wait` : Start services

`docker compose logs -f` : See logs

`docker compose ps` : See containers

`docker compose exec php bin/phpunit` : Run tests

`docker compose exec php bin/console -e test ...` : Execute commands for the test environment (create db, migrate...)

`XDEBUG_MODE=debug docker compose up --wait` : Enable XDebug
