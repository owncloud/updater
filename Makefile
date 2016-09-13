#
# Define NPM and check if it is available on the system.
#
NPM := $(shell command -v npm 2> /dev/null)
ifndef NPM
    $(error npm is not available on your system, please install npm)
endif

PHPUNIT="$(PWD)/lib/composer/phpunit/phpunit/phpunit"

updater_doc_files=COPYING-AGPL README.md
updater_src_files=application.php index.php
updater_src_dirs=app pub src vendor
updater_all_src=$(updater_src_files) $(updater_src_dirs) $(updater_doc_files)
build_dir=build
dist_dir=$(build_dir)/dist
COMPOSER_BIN=$(build_dir)/composer.phar
BOWER=$(build_dir)/node_modules/bower/bin/bower

# internal aliases
composer_deps=vendor/
composer_dev_deps=lib/composer/phpunit
node_modules=$(build_dir)/node_modules/
js_deps=pub/js/vendor/

#
# Catch-all rules
#
.PHONY: all
all: $(composer_dev_deps) install-js-deps

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
	mkdir $(js_deps)

.PHONY: install-js-deps
install-js-deps: $(js_deps)
	$(BOWER) install
	cp $(node_modules)/jquery/jquery.min.{map,js} $(js_deps)

.PHONY: update-js-deps
update-js-deps: $(js_deps)
	$(BOWER) update
	cp $(node_modules)/jquery/jquery.min.{map,js} $(js_deps)

.PHONY: clean-js-deps
clean-js-deps:
	rm -Rf $(js_deps)

#
# dist
#

$(dist_dir)/updater: $(composer_deps)  install-js-deps
	rm -Rf $@; mkdir -p $@
	cp -R $(updater_all_src) $@
	find $@/{vendor/,src/} -type d -iname Test? -print | xargs rm -Rf
	find $@/{vendor/,src/} -name travis -print | xargs rm -Rf
	find $@/{vendor/,src/} -name doc -print | xargs rm -Rf
	find $@/{vendor/,src/} -iname \*.sh -delete
	find $@/{vendor/,src/} -iname \*.exe -delete

.PHONY: dist
dist: $(dist_dir)/updater

.PHONY: clean-dist
clean-dist:
	rm -Rf $(dist_dir)

.PHONY: clean-build
clean-build:
	rm -Rf $(build_dir)
