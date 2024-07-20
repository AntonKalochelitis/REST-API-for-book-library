current_directory := $(shell pwd)

##################
# Variables
##################

include $(current_directory)/.env

DOCKER_COMPOSE = sudo docker-compose -f $(current_directory)/docker-compose.yml --env-file=$(current_directory)/.env

##################
# Docker compose
##################

up:
	${DOCKER_COMPOSE} up -d --build --remove-orphans

build:
	${DOCKER_COMPOSE} build

jwt_generate:
	sudo bin/console lexik:jwt:generate-keypair

start:
	${DOCKER_COMPOSE} start

stop:
	${DOCKER_COMPOSE} stop

rm:
	${DOCKER_COMPOSE} rm

restart: stop start

composer_update:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c 'composer update --no-interaction'

composer_install:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c 'composer install --no-interaction'

test_install:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c './bin/console doctrine:database:create --env=test'
	sudo docker exec -t ${PHP_FPM_HOST} bash -c './bin/console doctrine:migrations:migrate --env=test  --no-interaction'

test:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c './bin/phpunit'

make_migration:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c './bin/console make:migration'

migration:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c './bin/console doctrine:migrations:migrate --no-interaction'

migration_down:
	sudo docker exec -t ${PHP_FPM_HOST} bash -c './bin/console doctrine:migrations:migrate prev --no-interaction'
