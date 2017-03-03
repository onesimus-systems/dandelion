FROM alpine:3.5

MAINTAINER Lee Keitel <lfkeitel@usi.edu>

ENV TIMEZONE            America/Chicago
ENV PHP_MEMORY_LIMIT    512M
ENV MAX_UPLOAD          50M
ENV PHP_MAX_FILE_UPLOAD 200
ENV PHP_MAX_POST        100M

RUN	apk update && \
	apk upgrade && \
	apk add --update tzdata && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	apk add --update \
		bash \
		php7-mbstring \
		php7-session \
		php7-mcrypt \
		php7-openssl \
		php7-opcache \
		php7-gmp \
		php7-json \
		php7-dom \
		php7-pdo \
		php7-zip \
		php7-bcmath \
		php7-gd \
		php7-odbc \
		php7-pdo_mysql \
		php7-gettext \
		php7-bz2 \
		php7-iconv \
		php7-curl \
		php7-ctype \
		php7-apache2 && \

	# Set environments
	sed -i "s|;*date.timezone =.*|date.timezone = \"${TIMEZONE}\"|i" /etc/php7/php.ini && \
	sed -i "s|;*memory_limit =.*|memory_limit = ${PHP_MEMORY_LIMIT}|i" /etc/php7/php.ini && \
	sed -i "s|;*upload_max_filesize =.*|upload_max_filesize = ${MAX_UPLOAD}|i" /etc/php7/php.ini && \
	sed -i "s|;*max_file_uploads =.*|max_file_uploads = ${PHP_MAX_FILE_UPLOAD}|i" /etc/php7/php.ini && \
	sed -i "s|;*post_max_size =.*|post_max_size = ${PHP_MAX_POST}|i" /etc/php7/php.ini && \
	sed -i "s|;*cgi.fix_pathinfo=.*|cgi.fix_pathinfo= 0|i" /etc/php7/php.ini && \
	sed -i "s|;*expose_php =.*|expose_php = off|i" /etc/php7/php.ini && \
	sed -i "s|#LoadModule rewrite_module modules/mod_rewrite.so|LoadModule rewrite_module modules/mod_rewrite.so|i" /etc/apache2/httpd.conf && \

	# Cleaning up
	mkdir -p /var/www && \
	apk del tzdata && \
	rm -rf /var/cache/apk/*

RUN { \
		echo 'opcache.revalidate_freq=0'; \
		echo 'opcache.validate_timestamps=0'; \
		echo 'opcache.max_accelerated_files=2048'; \
		echo 'opcache.memory_consumption=192'; \
		echo 'opcache.interned_strings_buffer=16'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /etc/php7/conf.d/opcache.ini

COPY docker/apache.conf /etc/apache2/conf.d/dandelion-php.conf
COPY app /usr/src/dandelion/app
COPY bootstrap /usr/src/dandelion/bootstrap
COPY public /usr/src/dandelion/public
COPY vendor /usr/src/dandelion/vendor
COPY config/config.sample.php /usr/src/dandelion/config/config.sample.php
COPY config/config.defaults.php /usr/src/dandelion/config/config.defaults.php
COPY docker/docker-entrypoint.sh /usr/local/bin/

WORKDIR /var/www/dandelion
EXPOSE 80
VOLUME /var/www/dandelion

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/sbin/httpd", "-DFOREGROUND", "-f", "/etc/apache2/httpd.conf"]