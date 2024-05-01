#!/usr/bin/env bash

cd "$(dirname "${BASH_SOURCE[0]}")"

#XDEBUG_MODE=coverage ./vendor/bin/phpunit -c tests/phpunit.xml --coverage-text --fail-on-warning --display-warnings --colors=never
XDEBUG_MODE=develop,debug,coverage ./vendor/bin/phpunit -c tests/phpunit.xml --fail-on-warning --display-warnings $1
