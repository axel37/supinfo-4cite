[![CI](https://github.com/axel37/supinfo-4cite/actions/workflows/ci.yml/badge.svg)](https://github.com/axel37/supinfo-4cite/actions/workflows/ci.yml)

`docker compose build --no-cache` : Build images

`docker compose up --wait` : Start services

`docker compose logs -f` : See logs

`docker compose ps` : See containers

`docker compose exec php bin/phpunit` : Run tests

**Important :**

`docker compose exec php bin/console -e test ...` : Execute commands for the test environment (create db, migrate...)

`XDEBUG_MODE=debug docker compose up --wait` : Enable XDebug

`docker exec php bin/console lexik:jwt:generate-keypair ` : Generate keys for JWT authentication
