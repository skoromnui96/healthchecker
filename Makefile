placeholder:
	@:
.PHONY: placeholder

up: start
	@:
.PHONY: up

down: stop
	@:
.PHONY: down

start: stop
	@./docker/scripts/docker-start
.PHONY: start

stop:
	@./docker/scripts/docker-stop
.PHONY: stop

phpstan:
	@./docker/scripts/phpstan analyse -l max src/ -c /var/www/healthchecker/docker/phpstan/phpstan.neon
.PHONY: phpstan

php-cs-fixer:
	@./docker/scripts/php-cs-fixer fix
.PHONY: php-cs-fixer

phpunit:
	@./docker/scripts/phpunit tests
.PHONY: phpunit

pre-commit: php-cs-fixer phpstan phpunit
	@:
.PHONY: pre-commit
