.PHONY: install metrics test test-70 test-71

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

PHP_ARGS     := -dzend.assertions=1 -dzend_extension=$(XDEBUG) -dxdebug.coverage_enable=1

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

all: | install test metrics

clean: clean-build clean-vendor

clean-build: $(BUILD_DIR)
	$(RM) $(BUILD_DIR)

clean-vendor: $(VENDOR_DIR) composer.lock
	$(RM) $(VENDOR_DIR)
	$(DEL) composer.lock

install:
	$(COMPOSER) install

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

test:
ifeq ($(and $(PHP70),$(PHP71)),)
	$(COMPOSER) test
else
ifeq ($(call has_feature,output-sync),)
	make test-70 test-71
else
	make -j2 -O test-70 test-71
endif
endif
ifneq ($(PHPCPD),)
	@echo
	@echo $(LINE)
	$(PHPCPD) --fuzzy --min-tokens=10 $(SRC_DIR)
	@echo $(LINE)
	@echo
endif

test-70:
	@echo
	$(PHP70) $(PHP_ARGS) -v
	@echo
	$(PHP70) $(PHP_ARGS) $(TEST_CMD) --coverage-text=$(BUILD_DIR)$(DS)phpunit.tmp
	$(DEL) $(BUILD_DIR)$(DS)phpunit.tmp
	@echo

test-71:
ifneq ($(wildcard $(COVERAGE_DIR)),)
	$(RM) $(COVERAGE_DIR)
endif
	@echo
	$(PHP71) $(PHP_ARGS) -v
	@echo
	$(PHP71) $(PHP_ARGS) $(TEST_CMD) --coverage-html $(COVERAGE_DIR) --log-junit $(JUNIT_FILE)
	@echo
