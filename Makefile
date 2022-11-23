# Paramètre
SHELL         = sh
PROJECT       = Pasteur
GIT_AUTHOR    = Papoel
HTTP_PORT     = 8000
HOST_NAME	 = 127.0.0.0.1
DB_NAME       = db_pasteur
DB_USER       = root
DB_PASS       =
DB_PORT       = 3306
DB_SERVER     = MariaDB-10.8.3&charset=utf8mb4
DATABASE_URL  = \"mysql://$(DB_USER):$(DB_PASS)@$(HOST_NAME):$(DB_PORT)/$(DB_NAME)?serverVersion=$(DB_SERVER)\"

# Executables
EXEC_PHP      = php
COMPOSER      = composer
REDIS         = redis-cli
GIT           = git
YARN          = yarn
NPX           = npx

# Alias
SYMFONY       = $(EXEC_PHP) bin/console
SF            = symfony
SYMFONY_LINT  = $(SYMFONY) lint:

# Executables: vendors
PHPUNIT       = ./vendor/bin/phpunit
#PHPUNIT       = APP_ENV=test $(SF) php bin/phpunit
PHPSTAN       = ./vendor/bin/phpstan
PHP_CS_FIXER  = ./vendor/bin/php-cs-fixer
PHPMETRICS    = ./vendor/bin/phpmetrics

# Executables: uniquement en local
SYMFONY_BIN    = symfony
BREW           = brew
DOCKER         = docker
DOCKER_COMPOSE = docker-compose
DOCKER_RUN     = docker run

# Executables: uniquement en production
CERTBOT       = certbot

# PHPQA
PHPQA     = jakzal/phpqa
PHPQA_RUN = $(DOCKER_RUN) --init -it --rm -v $(PWD):/project -w /project $(PHPQA)

## —— 🤞 The Papoel Symfony Makefile 🤞 ————————————————————————————————————————————————————————————————————————————————————————————————————
help: ## Affiche l'écran d'aide
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Composer 🧙‍♂️             ———————————————————————————————————————————————————————————————————————————————————————————————————————————
install: composer.lock ## Installer les dépendances selon le fichier composer.lock actuel
	$(COMPOSER) install --no-progress --prefer-dist --optimize-autoloader
.PHONY: install

autoloader: ## Met à jour l'autoloader
	$(COMPOSER) dump-autoload --optimize

update: ## Mettre à jour les dépendances
	$(COMPOSER) update
.PHONY: update

recipes-update: ## Mettre à jour les recettes
	$(COMPOSER) recipes:update
.PHONY: recipes-update

## —— PHP 🐘 (macOS with brew)   ———————————————————————————————————————————————————————————————————————————————————————————————————————————
php-upgrade: ## Mettre à jour PHP à la dernière version
	$(BREW) upgrade php
.PHONY: php-upgrade

php-set-7-4: ## Définir php 7.4 comme la version actuelle de PHP
	$(BREW) unlink php
	$(BREW) link --overwrite php@7.4
.PHONY: php-set-7-4

php-set-8-0: ## Définir php 8.0 comme la version actuelle de PHP
	$(BREW) unlink php
	$(BREW) link --overwrite php@8.0
.PHONY: php-set-8-0

php-set-8-1: ## Définir php 8.1 comme la version actuelle de PHP
	$(BREW) unlink php
	$(BREW) link --overwrite php@8.1
.PHONY: php-set-8-1

## —— Symfony 🎵                 ———————————————————————————————————————————————————————————————————————————————————————————————————————————
sf: ## Lister toutes les commandes Symfony
	$(SYMFONY)
.PHONY: sf

cc: ## Videz le cache
	$(SYMFONY) cache:clear
	$(SYMFONY) cache:clear --no-warmup
	$(SYMFONY) cache:warmup

.PHONY: cc

warmup: ## Réchauffer la cache
	$(SYMFONY) cache:warmup
.PHONY: warmup

sf-dc: ## Create symfony database.
	$(SYMFONY) doctrine:database:create --if-not-exists
.PHONY: sf-dc

sf-dd: ## Drop symfony database.
	$(SYMFONY) doctrine:database:drop --if-exists --force
.PHONY: sf-dd

sf-mm: ## Make migrations.
	$(SYMFONY) make:migration
.PHONY: sf-mm

sf-dmm: ## Migrate.
	$(SYMFONY) doctrine:migrations:migrate --no-interaction
.PHONY: sf-dmm

sf-dfl: ## Load Fixtures.
	$(SYMFONY) doctrine:fixtures:load --no-interaction
.PHONY: sf-dmm

sf-migrations-status: ## Migrations status
	$(SYMFONY) doctrine:migrations:status

sf-routes: ## List all routes.
	$(SYMFONY) debug:router

sf-dsu: ## Migrate.
	$(SYMFONY) doctrine:schema:update --force
.PHONY: sf-dmm

sf-fixtures: ## Load fixtures.
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction
.PHONY: sf-fixtures

fix-perms: ## Fixer les permissions de tous les fichiers var
	chmod -R 777 var/*
.PHONY: fix-perms

assets: purge ## Installez les actifs avec des liens symboliques dans le dossier public.
	$(SYMFONY) assets:install public/  # Don't use "--symlink --relative" with a Docker env
.PHONY: assets

purge: ## Purger le cache et les journaux
	rm -rf var/cache/* var/logs/*
.PHONY: purge

## —— Symfony binaire 💻         ———————————————————————————————————————————————————————————————————————————————————————————————————————————
cert-install: ## Installez les certificats HTTPS locaux
	$(SYMFONY_BIN) server:ca:install
.PHONY: cert-install

serve: ## Servez l'application avec le support HTTPS (ajoutez "--no-tls" pour désactiver https)
	$(SYMFONY_BIN) serve --daemon --port=$(HTTP_PORT)
	$(eval CONFIRM := $(shell read -p "Faut-il exécuter le server Yarn ? [y/N] " CONFIRM && echo $${CONFIRM:-N}))
	$(SYMFONY_BIN) open:local
	@if [ "$(CONFIRM)" = "y" ]; then \
		$(YARN) dev-server; \
	fi
.PHONY: serve

unserve: ## Arrêtez le serveur web
	$(SYMFONY_BIN) server:stop
.PHONY: unserve

## —— elasticsearch 🔎           ———————————————————————————————————————————————————————————————————————————————————————————————————————————
populate: ## Réinitialisation et remplissage de l'index Elasticsearch
	$(SYMFONY) fos:elastica:reset
	$(SYMFONY) fos:elastica:populate --index=app
	$(SYMFONY) strangebuzz:populate
.PHONY: populate

## —— Docker 🐳                  ———————————————————————————————————————————————————————————————————————————————————————————————————————————
up: ## Démarrer le hub docker (PHP,caddy,MySQL,redis,adminer,elasticsearch)
	$(DOCKER_COMPOSE) up --detach
.PHONY: up

build: ## Construire les images (php + caddy)
	$(DOCKER_COMPOSE) build --pull --no-cache
.PHONY: build

down: ## Arrêtez le hub de docker
	$(DOCKER_COMPOSE) down --remove-orphans
.PHONY: down

logs: ## Afficher les journaux en temps réel
	$(DOCKER_COMPOSE) logs --tail=0 --follow
.PHONY: logs

wait-for-mysql: ## Attends que MySQL soit prêt
	bin/wait-for-mysql.sh
.PHONY: wait-for-mysql

wait-for-elasticsearch: ## Attends qu'Elasticsearch soit prêt
	bin/wait-for-elasticsearch.sh
.PHONY: wait-for-elasticsearch

bash: ## Se connecter au conteneur d'application
	$(DOCKER) container exec -it php bash
.PHONY: bash

## —— Projet ❤️                  ———————————————————————————————————————————————————————————————————————————————————————————————————————————
init: up init-db wait-for-elasticsearch populate serve ## Démarrer docker, charger les fixtures, remplir l'index Elasticsearch et démarrer le serveur web.

reload: init-db populate ## Chargement des fixtures et repeuplement de l'index Elasticsearch

start: up serve

stop: down unserve ## Arrêtez docker et le serveur Symfony

cc-redis: ## Videz tout le cache Redis
	$(REDIS) -p 6389 flushall
.PHONY: cc-redis

commands: ## Afficher toutes les commandes dans l'espace de nom du projet
	$(SYMFONY) list $(PROJECT)
.PHONY: commands

init-db: ## Construire la base de données, contrôler la validité des schémas, charger les fixtures.
	$(SYMFONY) doctrine:cache:clear-metadata
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create
	$(SYMFONY) doctrine:schema:create
	$(SYMFONY) doctrine:schema:update --force
	$(SYMFONY) doctrine:schema:validate
	$(SYMFONY) doctrine:fixtures:load --no-interaction
.PHONY: init-db

## —— Tests ✅                    ———————————————————————————————————————————————————————————————————————————————————————————————————————————
test: ## Créer un test avec la commande make test
	$(SYMFONY) make:test
.PHONY: test

# Execute the tests in folder tests/Unit or tests/Functional according to answer of the user
test-unit: ## Exécutez les tests unitaires
	@echo "\n==> Exécution des Tests Unitaires <==\n"
	$(EXEC_PHP) bin/phpunit --testdox --verbose tests/Unitaires

test-func: ## Exécutez les tests fonctionnels
	@echo "\n==> Exécution des Tests Fonctionnels <==\n"
	$(EXEC_PHP) bin/phpunit --testdox --verbose tests/Fonctionnels

test-all: phpunit.xml ## Lancer tous les tests
	$(PHPUNIT) --stop-on-failure

## ——Les normes de codage ✨      ———————————————————————————————————————————————————————————————————————————————————————————————————————————
cs: lint-php lint-js ## Effectuer tous les contrôles des normes de codage

static-analysis: stan ## Lancer l'analyse statique (PHPStan)

stan: ## Lancer PHPStan mais il faut créer un fichier phpstan.neon
	$(PHPSTAN) analyse -c configuration/phpstan.neon --memory-limit 1G
.PHONY: stan

lint-php: ## Lint des fichiers avec php-cs-fixer
	$(PHP_CS_FIXER) fix --allow-risky=yes --dry-run --config=php-cs-fixer.php
.PHONY: lint-php

fix-php: ## Corriger les fichiers avec php-cs-fixer
	$(PHP_CS_FIXER) fix --allow-risky=yes --config=php-cs-fixer.php
.PHONY: fix-php

## —— Déploiement & Prod 🚀      ———————————————————————————————————————————————————————————————————————————————————————————————————————————
deploy: ## Déploiement complet sans temps mort avec EasyDeploy (avec pré-deploy Git Hooks)
	test -z "`git status --porcelain`"                 # Prevent deploy if there are modified or added files
	test -z "`git diff --stat --cached origin/master`" # Prevent deploy if there is something to push on master
	$(SYMFONY) deploy -v                               # Deploy with EasyDeploy
.PHONY: deploy

env-check: ## Vérifier les principales variables ENV du projet
	printenv | grep -i app
.PHONY: env-check

## —— Yarn 🐱 / JavaScript       ———————————————————————————————————————————————————————————————————————————————————————————————————————————
dev: ## Rebuild assets pour l'environnement de développement
	$(YARN) install --check-files
	$(YARN) run encore dev
.PHONY: dev

watch: ## Surveiller les fichiers et build assets lorsque cela est nécessaire pour l'environnement de développement.
	$(YARN) run encore dev --watch
.PHONY: watch

encore: ## Build assets pour production
	$(YARN) run encore production
.PHONY: encore

lint-js: ## Normes de codage de Lints JS
	$(NPX) eslint assets/js
.PHONY: lint-js

fix-js: ## Corrige les fichiers JS
	$(NPX) eslint assets/js --fix
.PHONY: fix-js

## —— Rapports sur la qualité du code 📊 ———————————————————————————————————————————————————————————————————————————————————————————————————
report-metrics: ## Lancer le rapport phpmetrics
	$(PHPMETRICS) --report-html=var/phpmetrics/ src/
.PHONY: report-metrics

coverage: ## Créer le rapport de couverture de code avec PHPUnit
	$(EXEC_PHP) -d xdebug.enable=1 -d xdebug.mode=coverage -d memory_limit=-1 vendor/bin/phpunit --coverage-html=var/coverage
.PHONY: coverage

## ——————————————————‍      🤷 YOANDEV MAKEFILE 🤷‍     ————————————————————————————————————————————————————————————————————————————————————

## —— 🐛  PHPQA                  ———————————————————————————————————————————————————————————————————————————————————————————————————————————
qa-cs-fixer-dry-run: ## Détecter les erreurs dans le projet
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose --dry-run
.PHONY: qa-cs-fixer-dry-run

qa-cs-fixer: ## Fixer les erreurs détectées.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose
.PHONY: qa-cs-fixer

qa-phpstan-1: ## Run phpstan Level 1.
	$(PHPQA_RUN) phpstan analyse ./src --level=1
.PHONY: qa-phpstan

qa-phpstan-2: ## Run phpstan Level 2.
	$(PHPQA_RUN) phpstan analyse ./src --level=2
.PHONY: qa-phpstan

qa-phpstan-3: ## Run phpstan Level 3.
	$(PHPQA_RUN) phpstan analyse ./src --level=3
.PHONY: qa-phpstan

qa-phpstan-4: ## Run phpstan Level 4.
	$(PHPQA_RUN) phpstan analyse ./src --level=4
.PHONY: qa-phpstan

qa-phpstan-5: ## Run phpstan  Level 5.
	$(PHPQA_RUN) phpstan analyse ./src --level=5
.PHONY: qa-phpstan

qa-phpstan-6: ## Run phpstan Level 6.
	$(PHPQA_RUN) phpstan analyse ./src --level=6
.PHONY: qa-phpstan

qa-phpstan-7: ## Run phpstan Level 7.
	$(PHPQA_RUN) phpstan analyse ./src --level=7
.PHONY: qa-phpstan

qa-phpstan-8: ## Run phpstan Level 8.
	$(PHPQA_RUN) phpstan analyse ./src --level=8
.PHONY: qa-phpstan

qa-phpstan-9: ## Run phpstan Level 9.
	$(PHPQA_RUN) phpstan analyse ./src --level=9
.PHONY: qa-phpstan

qa-security-checker: ## Run security-checker.
	$(SF) security:check
.PHONY: qa-security-checker

qa-phpcpd: ## Run phpcpd (copy/paste detector).
	$(PHPQA_RUN) phpcpd ./src
.PHONY: qa-phpcpd

qa-php-metrics: ## Run php-metrics.
	$(PHPQA_RUN) phpmetrics --report-html=var/phpmetrics ./src
.PHONY: qa-php-metrics

qa-lint-twigs: ## Lint twig files.
	$(SYMFONY_LINT)twig ./templates
.PHONY: qa-lint-twigs

qa-lint-yaml: ## Lint yaml files.
	$(SYMFONY_LINT)yaml ./config
.PHONY: qa-lint-yaml

qa-lint-container: ## Lint container.
	$(SYMFONY_LINT)container
.PHONY: qa-lint-container

qa-lint-schema: ## Vérification du schéma de base de données.
	$(SYMFONY) doctrine:schema:validate --skip-sync -vvv --no-interaction
.PHONY: qa-lint-schema
#—————————————————————————————————————————————#

## —— 🔎  TESTS                  ———————————————————————————————————————————————————————————————————————————————————————————————————————————
tests: ## Exécuter les tests.
	@echo "\n==> Exécution de tous les Tests (Unitaires et Fonctionnelles) <==\n"
	$(PHPUNIT) --testdox
.PHONY: tests

tests-coverage: ## Exécuter les tests-coverage.
	$(PHPUNIT) --coverage-html var/coverage
.PHONY: tests-coverage
#---------------------------------------------#

## —— ⭐  AUTRE                   ———————————————————————————————————————————————————————————————————————————————————————————————————————————
before-commit: ## Exécuter avant de commit.
	$(MAKE) qa-cs-fixer
	$(MAKE) qa-phpstan-7
	$(MAKE) qa-security-checker
	$(MAKE) qa-phpcpd
	$(MAKE) qa-lint-twigs
	$(MAKE) qa-lint-yaml
	$(MAKE) qa-lint-container
	$(MAKE) qa-lint-schema
	$(MAKE) tests
.PHONY: before-commit

reset-db: ## Réinitialiser la base de données et créer un fichier de migration.
	$(eval CONFIRM := $(shell read -p "Êtes-vous sûr de vouloir réinitialiser la base de données ? [y/N] " CONFIRM && echo $${CONFIRM:-N}))
	@if [ "$(CONFIRM)" = "y" ]; then \
		$(MAKE) sf-dd; \
		$(MAKE) sf-dc; \
		$(MAKE) sf-mm; \
		$(MAKE) sf-dmm; \
	fi
.PHONY: reset-db

token: ## Générer un token et afficher un message de succès.
	$(EXEC_PHP) generate-token.php
.PHONY: token

## —— ⭐  SANDBOX                   ———————————————————————————————————————————————————————————————————————————————————————————————————————————
show-var:
	$(SF) var:export --multiline

init-db-test:
	$(SYMFONY) doctrine:cache:clear-metadata
	$(SYMFONY) doctrine:database:drop --force --if-exists
	$(SYMFONY) doctrine:database:create --env=test --if-not-exists
	$(SYMFONY) doctrine:schema:update --env=test --force
.PHONY: init-db-test

init-db-test-with-fixtures:
	$(MAKE) init-db-test
	$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test

rapport-tests:
	vendor/bin/phpunit --testdox-html $(PROJECT)_tests.html
.PHONY: rapport-tests

first-install: ## First install.
	$(COMPOSER) install    # Installe dépendances de Composer
	$(YARN)     install    # Installe dépendances de Yarn
	$(YARN)     build      # Compile les assets
	$(MAKE)     up         # Lance les containers
	$(MAKE)     env-local  # Créer le fichier .env.local et DATABASE_URL
	$(MAKE)     init-db    # Initialise la base de données
	$(MAKE)     serve      # Lance le serveur et ouvre le navigateur
	$(YARN)     dev-server # Lance le serveur de développement de Yarn
.PHONY: first-install

# create .env.local file and set APP_ENV=dev
env-local:
	touch .env.local
	@echo "DATABASE_URL=$(DATABASE_URL)" > .env.local
.PHONY: dev-env
