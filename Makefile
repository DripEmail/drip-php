.PHONY: test lint install

install:
	composer install

test:
	./vendor/bin/phpunit

lint:
	./vendor/bin/phpcs -s
