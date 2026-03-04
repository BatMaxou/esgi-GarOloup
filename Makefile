-include ./api/.env
-include ./api/.env.local

# --- CONTAINER ---
php = docker compose exec php
node = docker compose exec node

# --- HANDLE PARAMS ---
%:
	@:

ARGS = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

# --- PHP CS FIXER CONFIG ---
PHP_CS_FIXER_CONFIGURATION_FILE = ./api/.devops/lint/.php-cs-fixer.php
PHP_FIXER_VERSION = 3-php8.4
phpcsfixer = docker run --rm -v `pwd`:/code ghcr.io/php-cs-fixer/php-cs-fixer:${PHP_FIXER_VERSION}

# --- PHPSTAN CONFIG ---
PHPSTAN_CONFIGURATION_FILE = ./.devops/lint/phpstan.neon

# --- DEV COMMANDS ---
fixtures:
	@read -p "This action will delete all existing data, are you shure to continue? (y/n): " choice; \
	if [ "$$choice" = "y" ]; then \
		${MAKE} database; \
		${php} bin/console doctrine:fixtures:load --no-interaction; \
	else \
		echo "Aborted"; \
	fi
.PHONY: fixtures

vendor:
	${php} composer install
.PHONY: vendor

up:
	@docker compose up -d $(ARGS)
.PHONY: up

down:
	@docker compose down $(ARGS)
.PHONY: down

jwt:
	@${php} php bin/console lexik:jwt:generate-keypair
.PHONY: jwt

invalidate-tokens:
	@${php} php bin/console gesdinet:jwt:clear
.PHONY: invalidate-tokens

database:
	@${php} php bin/console doctrine:database:drop --if-exists --force
	@${php} php bin/console doctrine:database:create --if-not-exists
	@${php} php bin/console doctrine:migrations:migrate --no-interaction
	@${php} php bin/console doctrine:schema:update --force
.PHONY: database

# --- LINTERS ---
fixcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE)
.PHONY: fixcs

phpcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE) --dry-run
.PHONY: phpcs

phpstan:
	@$(php) vendor/bin/phpstan analyse --configuration=$(PHPSTAN_CONFIGURATION_FILE)
.PHONY: phpstan

php-lint:
	@${MAKE} phpcs
	@${MAKE} phpstan
.PHONY: php-lint

# --- TESTS ---
pretests:
	@${php} php bin/console c:c
	@${php} php bin/console doctrine:database:drop --if-exists --force --env=test
	@${php} php bin/console doctrine:database:create --env=test
	@${php} php bin/console doctrine:schema:update --force --env=test
	@${php} php bin/console lexik:jwt:generate-keypair --env=test --overwrite --no-interaction
.PHONY: pretests

tests:
	@${php} bin/phpunit
.PHONY: test

test-coverage:
	@${php} bin/phpunit --coverage-html var/coverage
.PHONY: test-coverage

# --- PROD DEPLOYMENT COMMANDS ---

deploy:
	@docker compose down $(ARGS)
	@docker compose pull $(ARGS)
	@docker compose up -d $(ARGS)
	@${php} php bin/console doctrine:migrations:migrate --no-interaction
.PHONY: deploy

# --- DEV UTILS ---
exec-db:
	@docker compose exec ${DB_HOST} mariadb -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME}
.PHONY: exec-db
