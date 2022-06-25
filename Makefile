name := rr_backend

.PHONY: app

compose = docker-compose -f .dev/docker-compose.yml -p="$(name)"
app = $(compose) exec -T app

app:
	@echo App container console:
	@$(compose) exec app bash


install: build up
	@echo installation complete.
update:
	@$(app) composer update
up:
	@$(compose) up -d
build:
	@$(compose) build
	@$(compose) run --rm app sh -c 'composer install'
destroy:
	@$(compose) down --rmi all


cs:
	@echo Code style checking..
	@$(app) vendor/bin/phpcs -v
cs-fix:
	@echo Trying to solve code style problems..
	@$(app) vendor/bin/phpcbf