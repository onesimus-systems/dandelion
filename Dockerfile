FROM php:7.1-apache

RUN docker-php-ext-install pdo_mysql opcache mbstring

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

COPY docker/apache.conf /etc/apache2/conf-available/dandelion-php.conf

RUN a2enmod rewrite expires && a2disconf docker-php && a2enconf dandelion-php

VOLUME /var/www/dandelion

COPY app /usr/src/dandelion
COPY bootstrap /usr/src/dandelion
COPY public /usr/src/dandelion
COPY vendor /usr/src/dandelion

RUN chown -R www-data:www-data /usr/src/dandelion

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]