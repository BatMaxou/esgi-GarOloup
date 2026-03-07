# --- ACCESS ENV VARS (used for exec-db) ---
-include ./api/.env
-include ./api/.env.local

# --- CONTAINER ---
php = docker compose exec php
node = docker compose exec node
run-node = docker compose run --rm node

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
install:
	@${MAKE} up
	@${MAKE} vendor
	@${MAKE} jwt
	@${MAKE} database
	@${MAKE} front-vendor
	@${MAKE} up node -d
.PHONY: install

fixtures:
	@read -p "This action will delete all existing data, are you shure to continue? (y/n): " choice; \
	if [ "$$choice" = "y" ]; then \
		${MAKE} database; \
		${php} bin/console doctrine:fixtures:load --no-interaction; \
	else \
		echo "Aborted"; \
	fi
.PHONY: fixtures

up:
	@docker compose up -d $(ARGS)
.PHONY: up

down:
	@docker compose down $(ARGS)
.PHONY: down

vendor:
	${php} composer install
.PHONY: vendor

jwt:
	@${php} php bin/console lexik:jwt:generate-keypair --overwrite
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

front-vendor:
	${run-node} pnpm install --frozen-lockfile --ignore-scripts=false
.PHONY: front-vendor

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

eslint:
	@${node} pnpm run lint
.PHONY: eslint

prettier:
	@${node} pnpm run format
.PHONY: prettier

prettier-fix:
	@${node} pnpm run format:fix
.PHONY: prettier-fix

front-lint:
	@${MAKE} eslint
	@${MAKE} prettier
.PHONY: front-lint

front-build:
	@${node} pnpm run build
.PHONY: front-build

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
