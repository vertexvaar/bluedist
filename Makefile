## Show this help
help:
	echo "$(EMOJI_interrobang) Makefile version $(VERSION) help "
	echo ''
	echo 'About this help:'
	echo '  Commands are ${BLUE}blue${RESET}'
	echo '  Targets are ${YELLOW}yellow${RESET}'
	echo '  Descriptions are ${GREEN}green${RESET}'
	echo ''
	echo 'Usage:'
	echo '  ${BLUE}make${RESET} ${YELLOW}<target>${RESET}'
	echo ''
	echo 'Targets:'
	awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")+1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${YELLOW}%-${TARGET_MAX_CHAR_NUM}s${RESET} ${GREEN}%s${RESET}\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Stop all containers
stop:
	echo "$(EMOJI_stop) Shutting down"
	docker compose stop
	sleep 0.4
	docker compose down --remove-orphans

## Removes all containers and volumes
destroy: stop
	echo "$(EMOJI_litter) Removing the project"
	docker compose down -v --remove-orphans

## Starts docker compose up -d
start:
	echo "$(EMOJI_up) Starting the docker project"
	docker compose up -d --build
	make urls

## Starts composer-install
composer:
	echo "$(EMOJI_package) Installing composer dependencies"
	docker compose exec php composer $(ARGS)

## Starts composer-install
composer-install:
	echo "$(EMOJI_package) Installing composer dependencies"
	docker compose exec php composer install

## Create necessary directories
create-dirs:
	echo "$(EMOJI_dividers) Creating required directories"

## Starts composer-install
composer-install-production:
	echo "$(EMOJI_package) Installing composer dependencies (without dev)"
	docker compose exec php composer install --no-dev -ao

install-mkcert:
	if [[ "$$OSTYPE" == "linux-gnu" ]]; then \
		if [[ "$$(command -v certutil > /dev/null; echo $$?)" -ne 0 ]]; then sudo apt install libnss3-tools; fi; \
		if [[ "$$(command -v mkcert > /dev/null; echo $$?)" -ne 0 ]]; then sudo curl -L https://github.com/FiloSottile/mkcert/releases/download/v1.4.1/mkcert-v1.4.1-linux-amd64 -o /usr/local/bin/mkcert; sudo chmod +x /usr/local/bin/mkcert; fi; \
	elif [[ "$$OSTYPE" == "darwin"* ]]; then \
	    BREW_LIST=$$(brew ls); \
		if [[ ! $$BREW_LIST == *"mkcert"* ]]; then brew install mkcert; fi; \
		if [[ ! $$BREW_LIST == *"nss"* ]]; then brew install nss; fi; \
	fi;
	mkcert -install > /dev/null

## Create SSL certificates for dinghy and starting project
create-certificate: install-mkcert
	echo "$(EMOJI_secure) Creating SSL certificates for dinghy http proxy"
	mkdir -p $(HOME)/.dinghy/certs/
	PROJECT=$$(echo "$${PWD##*/}" | tr -d '.'); \
	if [[ ! -f $(HOME)/.dinghy/certs/$$PROJECT.docker.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/$$PROJECT.docker.crt -key-file $(HOME)/.dinghy/certs/$$PROJECT.docker.key "*.$$PROJECT.docker"; fi;
	if [[ ! -f $(HOME)/.dinghy/certs/local.bluedist.com.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/local.bluedist.com.crt -key-file $(HOME)/.dinghy/certs/local.bluedist.com.key local.bluedist.com; fi;
	if [[ ! -f $(HOME)/.dinghy/certs/mail.local.bluedist.com.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/mail.local.bluedist.com.crt -key-file $(HOME)/.dinghy/certs/mail.local.bluedist.com.key mail.local.bluedist.com; fi;

## Initialize the docker setup
init-docker: create-dirs create-certificate
	echo "$(EMOJI_rocket) Initializing docker environment"
	docker compose pull
	docker compose up -d --build

## To start an existing project
install-project: stop add-hosts-entry init-docker composer-install
	echo "---------------------"
	echo ""
	echo "The project is online $(EMOJI_thumbsup)"
	echo ""
	echo 'Stop the project with "make stop"'
	echo ""
	echo "---------------------"
	make urls

## Print Project URIs
urls:
	PROJECT=$$(echo "$${PWD##*/}" | tr -d '.'); \
	SERVICES=$$(docker compose ps --services | grep '$(SERVICELIST)'); \
	LONGEST=$$(($$(echo -e "$$SERVICES\nFrontend:" | wc -L 2> /dev/null || echo 15)+2)); \
	echo "$(EMOJI_telescope) Project URLs:"; \
	echo ''; \
	printf "  %-$${LONGEST}s %s\n" "Frontend:" "https://local.bluedist.com/"; \
	printf "  %-$${LONGEST}s %s\n" "Mailpit:" "https://mail.local.bluedist.com/"; \
	for service in $$SERVICES; do \
		printf "  %-$${LONGEST}s %s\n" "$$service:" "https://$$service.$$PROJECT.docker/"; \
	done;

## Create the hosts entry for the custom project URL (non-dinghy convention)
add-hosts-entry:
	echo "$(EMOJI_monkey) Creating Hosts Entry (if not set yet)"
	SERVICES=$$(command -v getent > /dev/null && echo "getent ahostsv4" || echo "dscacheutil -q host -a name"); \
	if [ ! "$$($$SERVICES local.bluedist.com | grep 127.0.0.1 > /dev/null; echo $$?)" -eq 0 ]; then sudo bash -c 'echo "127.0.0.1 local.bluedist.com" >> /etc/hosts; echo "Entry was added"'; else echo 'Entry already exists'; fi; \
	if [ ! "$$($$SERVICES mail.local.bluedist.com | grep 127.0.0.1 > /dev/null; echo $$?)" -eq 0 ]; then sudo bash -c 'echo "127.0.0.1 mail.local.bluedist.com" >> /etc/hosts; echo "Entry was added"'; else echo 'Entry already exists'; fi;

## Log into the PHP container
login-php:
	echo "$(EMOJI_elephant) Logging into the PHP container"
	docker compose exec php bash

## Log into the httpd container
login-httpd:
	echo "$(EMOJI_helicopter) Logging into HTTPD Container"
	docker compose exec httpd bash

# SETTINGS
TARGET_MAX_CHAR_NUM := 25
MAKEFLAGS += --silent
SHELL := /bin/bash
VERSION := 1.0.0
ARGS = $(filter-out $@,$(MAKECMDGOALS))

# COLORS
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
BLUE   := $(shell tput -Txterm setaf 4)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

# EMOJIS (some are padded right with whitespace for text alignment)
EMOJI_litter := "🚮️"
EMOJI_interrobang := "⁉️ "
EMOJI_floppy_disk := "💾️"
EMOJI_dividers := "🗂️ "
EMOJI_up := "🆙️"
EMOJI_receive := "📥️"
EMOJI_robot := "🤖️"
EMOJI_stop := "🛑️"
EMOJI_package := "📦️"
EMOJI_secure := "🔐️"
EMOJI_explodinghead := "🤯️"
EMOJI_rocket := "🚀️"
EMOJI_plug := "🔌️"
EMOJI_leftright := "↔️ "
EMOJI_upright := "↗️ "
EMOJI_thumbsup := "👍️"
EMOJI_telescope := "🔭️"
EMOJI_monkey := "🐒️"
EMOJI_elephant := "🐘️"
EMOJI_dolphin := "🐬️"
EMOJI_helicopter := "🚁️"
EMOJI_broom := "🧹"
EMOJI_nutandbolt := "🔩"
EMOJI_controlknobs := "🎛️"

%:
	@:

cept-debug:
	docker compose exec -e PHP_IDE_CONFIG="serverName=local.bluedist.com" php php -dxdebug.mode=debug -dxdebug.remote_autostart=1 -dxdebug.start_with_request=yes -dxdebug.remote_host=host.docker.internal ./vendor/bin/codecept run

cept-run:
	docker compose exec php ./vendor/bin/codecept run

cept-run-unit:
	docker compose exec php ./vendor/bin/codecept run unit --recurse-includes

cept-run-functional:
	docker compose exec php ./vendor/bin/codecept run functional

cept-run-acceptance:
	docker compose exec php ./vendor/bin/codecept run acceptance

cept-build:
	docker compose exec php ./vendor/bin/codecept build
