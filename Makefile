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

## Initialize and start the project, then install dependencies
install-project: start composer-install status

## Start DDEV
start:
	echo "$(EMOJI_up) Starting the project"
	ddev start
	make urls

## Stop all containers
stop:
	echo "$(EMOJI_stop) Shutting down"
	ddev stop

## Removes all containers and volumes
destroy:
	echo "$(EMOJI_litter) Removing the project"
	ddev delete --omit-snapshot

## Run composer install
composer-install:
	echo "$(EMOJI_package) Installing composer dependencies"
	ddev composer install

## Run composer install without dev dependencies
composer-install-production:
	echo "$(EMOJI_package) Installing composer dependencies (without dev)"
	ddev composer install --no-dev -ao

## Print Project URIs
status:
	ddev status

## Log into the web container
bash:
	echo "$(EMOJI_elephant) Logging into the web container"
	ddev exec bash

## Install Playwright and browsers
playwright-install:
	npm install
	npx playwright install chromium

## Run all Playwright tests
playwright-run:
	npx playwright test

## Run Playwright tests with interactive UI
playwright-run-ui:
	npx playwright test --ui

## Open the last Playwright HTML report
playwright-report:
	npx playwright show-report

# SETTINGS
TARGET_MAX_CHAR_NUM := 25
MAKEFLAGS += --silent
SHELL := /bin/bash
VERSION := 1.0.0
ARGS = $(filter-out $@,$(MAKECMDGOALS))

# TEXT COLORS
BLACK   := $(shell tput -Txterm setaf 0)
RED     := $(shell tput -Txterm setaf 1)
GREEN   := $(shell tput -Txterm setaf 2)
YELLOW  := $(shell tput -Txterm setaf 3)
BLUE    := $(shell tput -Txterm setaf 4)
MAGENTA := $(shell tput -Txterm setaf 5)
CYAN    := $(shell tput -Txterm setaf 6)
WHITE   := $(shell tput -Txterm setaf 7)
RESET   := $(shell tput -Txterm sgr0)

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
EMOJI_crystal_ball := "🔮"
EMOJI_triangular_ruler := "📐"
EMOJI_ping_pong := "🏓"
EMOJI_face_with_rolling_eyes := "🙄"
EMOJI_eyes := "👀"
EMOJI_fire := "🔥"
EMOJI_runningshirt := "🎽"
EMOJI_evergreen_tree := "🌲"
EMOJI_luggage := "🧳"
EMOJI_fishing_pole := "🎣"
EMOJI_musical_score := "🎼"
EMOJI_nerd_face := "🤓"
EMOJI_digit_zero := "0️"
EMOJI_digit_one := "1️"
EMOJI_digit_two := "2️"
EMOJI_digit_three := "3️"
EMOJI_digit_four := "4️"
EMOJI_digit_seven := "7️"
EMOJI_pig_nose := "🐽"
EMOJI_customs := "🛃"
EMOJI_hot_face := "🥵"
EMOJI_cold_face := "🥶"
EMOJI_hourglass_not_done := "⏳"
EMOJI_bullseye := "🎯"
EMOJI_trumpet := "🎺"
EMOJI_video_camera := "📹"
