PHP=$(shell which php)
CURL=$(shell which curl)

composer.phar:
	$(CURL) -s http://getcomposer.org/installer | $(PHP)

install: composer.phar
	$(PHP) composer.phar install
