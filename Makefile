.DEFAULT_GOAL := help

COMPOSE    := docker compose
PHP        := $(COMPOSE) exec php
PHP_RUN    := $(COMPOSE) run --rm --no-deps php
NODE       := $(COMPOSE) exec frontend
NODE_RUN   := $(COMPOSE) run --rm --no-deps frontend
PLAYWRIGHT := $(COMPOSE) run --rm --no-deps playwright

c ?= php
PHP_SERVICES := php worker-enrich-cv-on-job-application-submitted

TEST_SUITES := unit integration functional smoke e2e mutation front front-e2e
DEFAULT_TEST_SUITES := unit integration functional smoke e2e front front-e2e
SUITE := $(word 2,$(MAKECMDGOALS))

ifeq (test,$(firstword $(MAKECMDGOALS)))
ifneq ($(SUITE),)
$(eval $(SUITE):;@:)
endif
endif

ENV_FILE := backend/.env

.PHONY: help init env build up down restart ps install sh logs db-migrate db-reset test stan deptrac lint fix check

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "  Test suites: make test <suite>  ->  $(TEST_SUITES)"
	@echo "  Pick a container: make sh c=frontend | make logs c=worker-enrich-cv-on-job-application-submitted"

init: env build install up db-migrate ## Create backend/.env, build images, install dependencies, start the stack and prepare the database
	@echo ""
	@echo "  App:         http://localhost:5173"
	@echo "  API:         http://localhost:8000/api/health"
	@echo "  RabbitMQ UI: http://localhost:15672 (viterbit_admin / viterbit_admin_password)"

env: $(ENV_FILE) ## Create backend/.env from backend/.env.example if it does not exist

$(ENV_FILE):
	cp backend/.env.example $@
	@echo "Created $@ from backend/.env.example"

build: ## Build Docker images (the Playwright one included)
	$(COMPOSE) --profile test build

up: ## Start the stack in the background
	$(COMPOSE) up -d --wait $(PHP_SERVICES) rabbitmq frontend

down: ## Stop and remove containers
	$(COMPOSE) down --remove-orphans

restart: down up ## Restart the stack

ps: ## Show container status
	$(COMPOSE) ps -a

install: ## Install backend (composer) and frontend (pnpm) dependencies, and enable the git hooks in .githooks
	$(PHP_RUN) composer install --no-interaction
	$(NODE_RUN) pnpm install --frozen-lockfile
	git config core.hooksPath .githooks

sh: ## Open a shell in a container (default php, use c=<service>)
	$(COMPOSE) exec $(c) sh

logs: ## Follow logs (all services, or c=<service>)
	$(COMPOSE) logs -f $(if $(filter command line,$(origin c)),$(c),)

db-migrate: ## Create the databases if needed and run migrations (dev and test)
	$(PHP) bin/console doctrine:database:create --if-not-exists
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) bin/console doctrine:database:create --if-not-exists --env=test
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction --env=test

db-reset: ## Drop the PostgreSQL databases (dev and test) and migrate again
	$(COMPOSE) stop $(PHP_SERVICES)
	$(PHP_RUN) bin/console doctrine:database:drop --force --if-exists
	$(PHP_RUN) bin/console doctrine:database:drop --force --if-exists --env=test
	$(COMPOSE) up -d --wait $(PHP_SERVICES)
	$(MAKE) --no-print-directory db-migrate

test: ## Run all test suites (except mutation) or one: make test <suite>
	@$(if $(SUITE),$(if $(filter $(SUITE),$(TEST_SUITES)),,$(error Unknown suite "$(SUITE)". Valid suites: $(TEST_SUITES))))
	@$(MAKE) --no-print-directory $(addprefix test-,$(or $(SUITE),$(DEFAULT_TEST_SUITES)))

test-unit test-integration test-functional test-smoke test-e2e:
	$(PHP) vendor/bin/phpunit --testsuite $(subst test-,,$@)

test-mutation:
	$(PHP) vendor/bin/infection --threads=max --show-mutations

test-front:
	$(PLAYWRIGHT) pnpm test

test-front-e2e:
	$(PLAYWRIGHT) pnpm test:e2e

stan: ## Run PHPStan (level 9)
	$(PHP) vendor/bin/phpstan analyse --memory-limit=1G

deptrac: ## Check architecture layers and bounded context boundaries
	$(PHP) vendor/bin/deptrac analyse --config-file=deptrac.layers.yaml --no-progress
	$(PHP) vendor/bin/deptrac analyse --config-file=deptrac.contexts.yaml --no-progress

lint: ## Check code style (php-cs-fixer dry run, ESLint, tsc) and the Doctrine mapping
	$(PHP) vendor/bin/php-cs-fixer check --diff
	$(PHP) bin/console doctrine:schema:validate --skip-sync
	$(NODE) pnpm lint

fix: ## Fix code style: php-cs-fixer and ESLint --fix
	$(PHP) vendor/bin/php-cs-fixer fix
	$(NODE) pnpm lint:fix

check: lint stan deptrac ## Run everything CI would run: lint, stan, deptrac, all tests and mutation
	@$(MAKE) --no-print-directory test
	@$(MAKE) --no-print-directory test-mutation
