# ------------------------------------------------------------------------------ Config

# ------------------------------------------------------------------------------ Helper

has_feature = $(if $(filter $1,$(.FEATURES)),$1)
which       = $(shell command -v $1 2>/dev/null)

# ------------------------------------------------------------------------------ Default

all: test

# ------------------------------------------------------------------------------ Help

define HELP =
Usage: make [TARGET]

Targets:
    clean         remove ALL build artifacts and dependencies
    help          `list` alias
    install       `update` alias
    list          list available make targets
    phpcpd        analyze PHP code for violations of the DRY principle
    phpmetrics    analyze PHP files and create a PhpMetrics report
    test          run ALL tests
    test-70       run tests with PHP 7.0
    test-71       run tests with PHP 7.1
    up-dist       `update` alias
    up-low        update dependencies to LOWEST versions
    update        update dependencies to LATEST versions

The following sequence of targets is executed if no target is given:

1. up-dist
2. test-70 + test-71
3. up-low
4. test-70 + test-71
5. phpmetrics
6. phpcpd

Both `test-70` and `test-71` require that the respective PHP executables are
in PATH as either `php70` or `php7.0`, and `php71` or `php7.1`. The former is
the default on Mac systems with brew and the later is the default on Ubuntu
systems. Windows users are encouraged to ensure that the executables are to be
found as `php70`/`php71` to ensure that the do not collide with the executables
of the Linux subsystem.

`composer test` will be executed, if the PHP executables are no in PATH as
described in the previous paragraph.

The `phpmetrics` and `phpcpd` targets require that their respective executables
are in PATH.

endef

help: list

.PHONY: list
list:
	@: $(info $(HELP))

# ------------------------------------------------------------------------------ Composer

VENDOR_DIR      := vendor/
AUTOLOAD_SCRIPT := $(VENDOR_DIR)autoload.php
COMPOSER_DIST   := $(VENDOR_DIR).dist
COMPOSER_JSON   := composer.json
COMPOSER_LOCK   := composer.lock
COMPOSER_LOWEST := $(VENDOR_DIR).lowest

define composer_up =
$(call which,composer) update --no-plugins --no-progress --no-scripts --no-suggest --prefer-$(subst .,,$(suffix $@))
[ $@ == $(COMPOSER_DIST) ] && rm -f $(COMPOSER_LOW) || rm -f $(COMPOSER_DIST)
touch $@
endef

install: $(AUTOLOAD_SCRIPT)
up-dist: $(COMPOSER_DIST)
up-low: $(COMPOSER_LOWEST)
update: $(COMPOSER_DIST)

$(AUTOLOAD_SCRIPT): $(COMPOSER_LOCK)
$(COMPOSER_LOCK): $(COMPOSER_JSON)
$(COMPOSER_JSON): $(COMPOSER_DIST)

$(COMPOSER_DIST):
	$(composer_up)

$(COMPOSER_LOWEST):
	$(composer_up)

# ------------------------------------------------------------------------------ Testing

PHPUNIT_CONFIG := phpunit.xml.dist

PHP70 := $(call which,php70)
ifeq ($(PHP70),)
PHP70 := $(call which,php7.0)
ifeq ($(PHP70),)
PHP70 :=
endif
endif

PHP71 := $(call which,php71)
ifeq ($(PHP71),)
PHP71 := $(call which,php7.1)
ifeq ($(PHP71),)
PHP71 :=
endif
endif

test: $(PHPUNIT_CONFIG)
ifeq ($(and $(PHP70),$(PHP71)),)
	$(COMPOSER) test
else
ifeq ($(call has_feature,output-sync),)
	$(MAKE) up-low test-70 test-71
	$(MAKE) up-dist test-70 test-71
else
	$(MAKE) up-low
	$(MAKE) -j2 -O test-70 test-71
	$(MAKE) up-dist
	$(MAKE) -j2 -O test-70 test-71
endif
endif

BUILD_DIR  := build/
LOGS_DIR   := $(BUILD_DIR)logs/
HTML_DIR   := $(LOGS_DIR)coverage/
JUNIT_FILE := $(LOGS_DIR)junit.xml
TEST_DIR   := tests/
PHPUNIT    := vendor/phpunit/phpunit/phpunit --colors=always

ifeq ($(OS),Windows_NT)
php_extension = php_$1.dll
else
php_extension = $1.so
endif

PHP_ARGS := -dzend.assertions=1 -dzend_extension=$(call php_extension,xdebug) -dxdebug.coverage_enable=1
PHP70    := $(PHP70) $(PHP_ARGS)
PHP71    := $(PHP71) $(PHP_ARGS)

test-70: $(PHPUNIT_CONFIG)
	$(PHP70) $(PHPUNIT) --coverage-text=$(BUILD_DIR)/.$@.tmp
	@ $(RM) $(BUILD_DIR)/.$@.tmp

$(JUNIT_FILE): test-71
test-71: $(PHPUNIT_CONFIG)
	$(PHP71) $(PHPUNIT) --coverage-html $(HTML_DIR) --log-junit $(JUNIT_FILE)

# ------------------------------------------------------------------------------ Analysis

SRC_DIR     := src/
METRICS_DIR := $(LOGS_DIR)metrics/

phpmetrics: $(JUNIT_FILE)
	$(call which,phpmetrics) --report-html=$(METRICS_DIR) --junit=$(JUNIT_FILE) $(SRC_DIR)

phpcpd:
	$(call which,phpcpd) --fuzzy --min-tokens=10 $(SRC_DIR)

# ------------------------------------------------------------------------------ Clean-up

.PHONY: clean
clean:
	rm -fr $(BUILD_DIR) $(VENDOR_DIR) $(COMPOSER_LOCK)
