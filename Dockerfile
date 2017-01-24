FROM php:7.1-apache

RUN docker-php-ext-install pdo_mysql opcache mbstring

# These settings are for production, cache lots, don't check for changes
RUN { \
		echo 'opcache.revalidate_freq=0'; \
		echo 'opcache.validate_timestamps=0'; \
		echo 'opcache.max_accelerated_files=2048'; \
		echo 'opcache.memory_consumption=192'; \
		echo 'opcache.interned_strings_buffer=16'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache.ini

COPY docker/apache.conf /etc/apache2/conf-available/dandelion-php.conf

RUN a2enmod rewrite expires && a2disconf docker-php && a2enconf dandelion-php

VOLUME /var/www/dandelion

COPY app /usr/src/dandelion/app
COPY bootstrap /usr/src/dandelion/bootstrap
COPY public /usr/src/dandelion/public
COPY vendor /usr/src/dandelion/vendor
COPY config/config.sample.php /usr/src/dandelion/config/config.sample.php
COPY config/config.defaults.php /usr/src/dandelion/config/config.defaults.php

RUN chown -R www-data:www-data /usr/src/dandelion

COPY docker/docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]