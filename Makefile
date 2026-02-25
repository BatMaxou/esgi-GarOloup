-include .env
-include .env.local

# --- HANDLE PARAMS ---
%:
	@:

ARGS = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

# --- PHP CS FIXER CONFIG ---
PHP_CS_FIXER_CONFIGURATION_FILE = ./api/.devops/lint/.php-cs-fixer.php
PHP_FIXER_VERSION = 3-php8.4
phpcsfixer = docker run --rm -v `pwd`:/code ghcr.io/php-cs-fixer/php-cs-fixer:${PHP_FIXER_VERSION}

# --- DEV COMMANDS ---
fixtures:
	@read -p "This action will delete all existing data, are you shure to continue? (y/n): " choice; \
	if [ "$$choice" = "y" ]; then \
		${MAKE} database; \
		docker compose exec php php bin/console doctrine:fixtures:load --no-interaction; \
	else \
		echo "Aborted"; \
	fi
.PHONY: fixtures

vendor:
	@docker compose exec php composer install
.PHONY: vendor

up:
	@docker compose up -d $(ARGS)
.PHONY: up

down:
	@docker compose down $(ARGS)
.PHONY: down

jwt:
	@docker compose exec php php bin/console lexik:jwt:generate-keypair
.PHONY: jwt

invalidate-tokens:
	@docker compose exec php php bin/console gesdinet:jwt:clear
.PHONY: invalidate-tokens

database:
	@docker compose exec php php bin/console doctrine:database:drop --if-exists --force
	@docker compose exec php php bin/console doctrine:database:create --if-not-exists
	@docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
.PHONY: database

# --- LINTERS ---
fixcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE)
.PHONY: fixcs

phpcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE) --dry-run
.PHONY: phpcs

php-lint:
	@${MAKE} phpcs
.PHONY: php-lint

# --- TESTS ---
pretests:
	@docker compose exec php php bin/console doctrine:database:drop --if-exists --force --env=test
	@docker compose exec php php bin/console doctrine:database:create --env=test
	@docker compose exec php php bin/console doctrine:schema:update --force --env=test
	@docker compose exec php php bin/console lexik:jwt:generate-keypair --env=test
.PHONY: pretests

tests:
	@docker compose exec php php bin/phpunit
.PHONY: test

test-coverage:
	@docker compose exec php php bin/phpunit --coverage-html var/coverage
.PHONY: test-coverage

# --- DEV UTILS ---

exec-db:
	@docker compose exec ${DB_HOST} mariadb -u${DB_USER} -p${DB_PASSWORD} ${DB_NAME}
.PHONY: exec-db
