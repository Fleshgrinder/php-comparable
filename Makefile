.PHONY: clean install metrics test test-70 test-71 update-dist update-lowest

XDEBUG := php_xdebug.dll
ifdef ComSpec
DEVNUL := NUL
DEL    := DEL
DS     := $(strip \\)
EXT    := .bat
RM     := RMDIR /Q /S
WHICH  := WHERE
else
DEVNUL := /dev/null
DEL    := rm
DS     := /
EXT    :=
RM     := rm -r
WHICH  := command -v
ifneq ($(OS),Windows_NT)
XDEBUG := xdebug.so
endif
endif

which       = $(shell $(WHICH) $(1) 2>$(DEVNUL))
has_feature = $(if $(filter $(1),$(.FEATURES)),$(1))

LINE         := --------------------------------------------------------------------------------

BUILD_DIR    := build
LOGS_DIR     := $(BUILD_DIR)$(DS)logs
VENDOR_DIR   := vendor
COVERAGE_DIR := $(LOGS_DIR)$(DS)coverage
JUNIT_FILE   := $(LOGS_DIR)$(DS)junit.xml
METRICS_DIR  := $(LOGS_DIR)$(DS)metrics
SRC_DIR      := src

COMPOSER     := $(call which,composer$(EXT))
PHPCPD       := $(call which,phpcpd$(EXT))
PHPMETRICS   := $(call which,phpmetrics$(EXT))
TEST_CMD     := vendor$(DS)phpunit$(DS)phpunit$(DS)phpunit --colors=always

COMPOSER_ARGS := --no-plugins --no-progress --no-scripts --no-suggest
PHP_ARGS      := -dzend.assertions=1 -dzend_extension=$(XDEBUG) -dxdebug.coverage_enable=1

PHP70 := $(call which,php70$(EXT))
ifeq ($(PHP70),)
PHP70 := $(call which,php7.0$(EXT))
ifeq ($(PHP70),)
PHP70 :=
endif
endif

PHP71 := $(call which,php71$(EXT))
ifeq ($(PHP71),)
PHP71 := $(call which,php7.1$(EXT))
ifeq ($(PHP71),)
PHP71 :=
endif
endif

all: | test metrics

clean:
ifneq ($(wildcard $(BUILD_DIR)),)
	$(RM) $(BUILD_DIR)
endif
ifneq ($(wildcard $(VENDOR_DIR)),)
	$(RM) $(VENDOR_DIR)
endif
ifneq ($(wildcard composer.lock)),)
	$(DEL) composer.lock
endif

install: composer.json
	$(COMPOSER) install $(COMPOSER_ARGS) --prefer-dist

metrics:
ifneq ($(PHPMETRICS),)
ifneq ($(wildcard $(METRICS_DIR)),)
	$(RM) $(METRICS_DIR)
endif
ifeq ($(wildcard $(JUNIT_FILE)),)
	$(PHPMETRICS) --report-html=$(METRICS_DIR) $(SRC_DIR)
else
	$(PHPMETRICS) --report-html=$(METRICS_DIR) --junit=$(JUNIT_FILE) $(SRC_DIR)
endif
endif
ifneq ($(PHPCPD),)
	$(PHPCPD) --fuzzy --min-tokens=10 $(SRC_DIR)
endif

test: phpunit.xml.dist
ifeq ($(and $(PHP70),$(PHP71)),)
	$(COMPOSER) test
else
ifeq ($(call has_feature,output-sync),)
	make update-lowest test-70 test-71 update-dist test-70 test-71
else
	make update-lowest
	make -j2 -O test-70 test-71
	make update-dist
	make -j2 -O test-70 test-71
endif
endif

test-70: phpunit.xml.dist
	$(PHP70) $(PHP_ARGS) $(TEST_CMD) --coverage-text=$(BUILD_DIR)$(DS)phpunit.tmp
	$(DEL) $(BUILD_DIR)$(DS)phpunit.tmp

test-71: phpunit.xml.dist
ifneq ($(wildcard $(COVERAGE_DIR)),)
	$(RM) $(COVERAGE_DIR)
endif
	$(PHP71) $(PHP_ARGS) $(TEST_CMD) --coverage-html $(COVERAGE_DIR) --log-junit $(JUNIT_FILE)

update-dist: composer.json
	$(COMPOSER) update $(COMPOSER_ARGS) --prefer-dist

update-lowest: composer.json
	$(COMPOSER) update $(COMPOSER_ARGS) --prefer-lowest
