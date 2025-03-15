.PHONY: setup up down attach start test check code-check clear

# USER VARIABLES / PROJECT VARIABLES
PHP_CONTAINER_NAME = blue-php

DOCKER_COMPOSE = docker compose -f docker-compose.yml -f docker-compose-dev.yml
DOCKER_COMPOSE_UP =  ${DOCKER_COMPOSE} up -d --force-recreate --remove-orphans
DOCKER_COMPOSE_BUILD = ${DOCKER_COMPOSE} build --no-cache
DOCKER_COMPOSE_DOWN =  ${DOCKER_COMPOSE} down
DOCKER_RUN_COMMAND = docker exec -it ${PHP_CONTAINER_NAME}
RUN_SERVER_SWOOLE = swoole:server:run

CONSOLE = ./bin/console
PHPUNIT = ./vendor/bin/phpunit -c ./phpunit.xml.dist
COV_CHECK = ./vendor/bin/coverage-check .analysis/phpunit/coverage/coverage.xml 20
PHP_STAN = ./vendor/bin/phpstan
PHP_MD = ./vendor/bin/phpmd src/ text phpmd_ruleset.xml

setup:
	${DOCKER_COMPOSE_BUILD} && \
	${DOCKER_COMPOSE_UP} && \
	${DOCKER_RUN_COMMAND} composer install

start:
	${DOCKER_COMPOSE_UP} && \
	${DOCKER_RUN_COMMAND} ${CONSOLE} ${RUN_SERVER_SWOOLE}

start-prod:
	docker compose -f docker-compose.yml -f docker-compose-prod.yml build --no-cache && \
	docker compose -f docker-compose.yml -f docker-compose-prod.yml up -d --force-recreate --remove-orphans
	docker compose -f docker-compose.yml -f docker-compose-prod.yml exec php bin/console app:load-external-data --no-interaction

up:
	${DOCKER_COMPOSE_UP}

attach:
	${DOCKER_RUN_COMMAND} bash

down:
	${DOCKER_COMPOSE_DOWN}

test:
	php-ext-disable xdebug
	php-ext-enable pcov
	${PHPUNIT} --stop-on-failure && ${COV_CHECK} && \
	cp -f .analysis/phpunit/coverage/junit.xml .analysis/phpunit/coverage/phpunit.junit.xml && \
	php-ext-disable pcov
	php-ext-enable xdebug

code-check:
	${CONSOLE} cache:clear && ${PHP_STAN} && ${PHP_MD}

clear:
	rm -rf vendor composer.lock .analysis .phpunit.result.cache && composer install

check: clear code-check test

help:
	# Usage:
	#   make <target> [OPTION=value]
	#
	# Targets:
	#   setup ...........................Sets up docker & app
	#   up ..............................Up Docker
	#   down ............................Down Docker
	#   attach ..........................attaches to docker PHP container
	#   test ........................... Tests
	#   code-check ..................... Complete code check
	#   start .......................... Runs Swoole web server
	#   check .......................... All checks before push
	#   clear .... ..................... Complete code check


