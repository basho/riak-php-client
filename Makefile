.PHONY: all unit-test integration-test security-test
.PHONY: install-composer install-deps help

all: test

test: unit-test integration-test

unit-test:
	@php ./vendor/bin/phpunit --testsuite=unit-tests

integration-test:
	@php ./vendor/bin/phpunit --testsuite=functional-tests

security-test:
	@php ./vendor/bin/phpunit  --testsuite=security-tests

install-deps:
	@./composer install

install-composer:
	@rm -f ./composer.phar ./composer
	@php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	@php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	@php ./composer-setup.php --filename=composer
	@rm -f ./composer-setup.php

help:
	@echo ''
	@echo ' Targets:'
	@echo '-----------------------------------------------------------------'
	@echo ' all                          - Run all tests                    '
	@echo ' install-deps                 - Install required dependencies    '
	@echo ' install-composer             - Installs composer                '
	@echo ' test                         - Run unit & integration tests     '
	@echo ' unit-test                    - Run unit tests                   '
	@echo ' integration-test             - Run integration tests            '
	@echo ' security-test                - Run security tests               '
	@echo '-----------------------------------------------------------------'
	@echo ''
