SHELL := /bin/bash

#
# Define NPM and check if it is available on the system.
#
NPM := $(shell command -v npm 2> /dev/null)
ifndef NPM
    $(error npm is not available on your system, please install npm)
endif

updater_doc_files=COPYING-AGPL README.md CHANGELOG.md
updater_src_files=application.php index.php
updater_src_dirs=app pub src vendor
updater_all_src=$(updater_src_files) $(updater_src_dirs) $(updater_doc_files)
build_dir=build
dist_dir=$(build_dir)/dist
COMPOSER_BIN=$(build_dir)/composer.phar
BOWER=$(build_dir)/node_modules/bower/bin/bower
PHPUNITDBG=phpdbg -qrr -d memory_limit=4096M -d zend.enable_gc=0
PHP_CS_FIXER=php -d zend.enable_gc=0 vendor-bin/owncloud-codestyle/vendor/bin/php-cs-fixer

# internal aliases
composer_deps=vendor/
composer_dev_deps=lib/composer/phpunit
js_deps=pub/js/vendor/

#
# Catch-all rules
#
.PHONY: all
all: $(composer_dev_deps) $(js_deps)

.PHONY: clean
clean: clean-composer-deps clean-js-deps clean-dist clean-build


#
# Basic required tools
#
$(COMPOSER_BIN):
	mkdir $(build_dir)
	cd $(build_dir) && curl -sS https://getcomposer.org/installer | php

$(BOWER):
	$(NPM) install --prefix $(build_dir) bower
	touch $(BOWER)


#
# ownCloud updater PHP dependencies
#
$(composer_deps): $(COMPOSER_BIN) composer.json composer.lock
	php $(COMPOSER_BIN) install --no-dev

$(composer_dev_deps): $(COMPOSER_BIN) composer.json composer.lock
	php $(COMPOSER_BIN) install --dev

.PHONY: clean-composer-deps
clean-composer-deps:
	rm -f $(COMPOSER_BIN)
	rm -Rf $(composer_deps)

.PHONY: update-composer
update-composer: $(COMPOSER_BIN)
	rm -f composer.lock
	php $(COMPOSER_BIN) install --prefer-dist

#
# ownCloud updater JavaScript dependencies
#
$(js_deps): $(BOWER) bower.json
	$(BOWER) install
	touch $(js_deps)

.PHONY: install-js-deps
install-js-deps: $(js_deps)

.PHONY: update-js-deps
update-js-deps: $(js_deps)


.PHONY: clean-js-deps
clean-js-deps:
	rm -Rf $(js_deps)

##------------
## Tests
##------------

.PHONY: test-lint
test-lint:             ## Run php lint to check for syntax errors
test-lint:
	find . -name \*.php -exec php -l "{}" \;

.PHONY: test-php-unit
test-php-unit:             ## Run php unit tests
test-php-unit: vendor/bin/phpunit
	cd src/Tests && ../../vendor/bin/phpunit --configuration phpunit.xml --testsuite 'ownCloud - Standalone Updater Tests'

.PHONY: test-php-unit-dbg
test-php-unit-dbg:         ## Run php unit tests using phpdbg
test-php-unit-dbg: vendor/bin/phpunit
	make
	$(PHPUNITDBG) ./vendor/bin/phpunit --configuration ./src/Tests/phpunit.xml --testsuite 'ownCloud - Standalone Updater Tests'

.PHONY: test-php-style
test-php-style:            ## Run php-cs-fixer and check owncloud code-style
test-php-style: vendor-bin/owncloud-codestyle/vendor
	$(PHP_CS_FIXER) fix -v --diff --diff-format udiff --allow-risky yes --dry-run

.PHONY: test-php-style-fix
test-php-style-fix:        ## Run php-cs-fixer and fix code style issues
test-php-style-fix: vendor-bin/owncloud-codestyle/vendor
	$(PHP_CS_FIXER) fix -v --diff --diff-format udiff --allow-risky yes

#
# dist
#

$(dist_dir)/updater: $(composer_deps)  $(js_deps)
	rm -Rf $@; mkdir -p $@
	cp -R $(updater_all_src) $@
	find $@ -name .gitkeep -delete
	find $@ -name .gitignore -delete
	find $@/{vendor/,src/} -type d -iname Test? -print | xargs rm -Rf
	find $@/{vendor/,src/} -name travis -print | xargs rm -Rf
	find $@/{vendor/,src/} -name doc -print | xargs rm -Rf
	find $@/{vendor/,src/} -iname \*.sh -delete
	find $@/{vendor/,src/} -iname \*.exe -delete
	find $@/pub/js/vendor/jquery \! -name jquery.min.* -type f -exec rm -f {} +
	find $@/pub/js/vendor/jquery/* -type d -exec rm -rf {} +

$(dist_dir)/updater.tar.gz: $(dist_dir)/updater
	cd $(dist_dir) && tar --format=gnu --owner=nobody --group=nogroup -czf updater.tar.gz updater

.PHONY: dist
dist: $(dist_dir)/updater.tar.gz

.PHONY: clean-dist
clean-dist:
	rm -Rf $(dist_dir)

.PHONY: clean-build
clean-build:
	rm -Rf $(build_dir)

#
# Dependency management
#--------------------------------------
composer.lock: composer.json
	@echo composer.lock is not up to date.

vendor: composer.lock
	composer install --no-dev

vendor/bin/phpunit: composer.lock
	composer install
	composer require --dev phpunit/phpunit ^7.5

vendor/bamarni/composer-bin-plugin: composer.lock
	composer install

vendor-bin/owncloud-codestyle/vendor: vendor/bamarni/composer-bin-plugin vendor-bin/owncloud-codestyle/composer.lock
	composer bin owncloud-codestyle install --no-progress

vendor-bin/owncloud-codestyle/composer.lock: vendor-bin/owncloud-codestyle/composer.json
	@echo owncloud-codestyle composer.lock is not up to date.

