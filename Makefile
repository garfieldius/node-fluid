install: fluid.phar

clean:
	cd php && rm -rf vendor composer.phar fluid.phar
	rm -rf fluid.phar

.PHONY: install clean

fluid.phar: php/fluid.phar
	cp -a php/fluid.phar fluid.phar

php/fluid.phar: php/vendor/autoload.php
	cd php && php -dphar.readonly=0 compile.php

php/vendor/autoload.php: php/composer.phar
	cd php && php composer.phar install --no-dev -o --prefer-dist

php/composer.phar:
	curl -sSLo php/composer.phar https://github.com/composer/composer/releases/download/1.6.5/composer.phar
