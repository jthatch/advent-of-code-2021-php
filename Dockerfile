FROM php:8.1-cli

ENV XDEBUG_MODE=off

RUN apt update -y \
	&& apt-get install -y git unzip zip \
	&& pecl channel-update pecl.php.net \
    \
	# Opcache
	&& docker-php-ext-configure opcache --enable-opcache \
	&& docker-php-ext-enable opcache \
	\
	# XDebug
	&& pecl install xdebug \
	&& docker-php-ext-enable xdebug \
	\
	# bcmath
	&& docker-php-ext-configure bcmath --enable-bcmath \
	&& docker-php-ext-install bcmath \
    \
	# gmp
	&& apt install -y libgmp-dev \
	&& docker-php-ext-install gmp \
    \
    # cleanup
	&& rm -rf /var/cache/apk/* \
	&& docker-php-source delete

# copy our xdebug to the container
USER root
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# composer
# disabled to keep sizes down, we'll need git and zip added to the image in order to use composer
COPY --from=composer:2.2.6 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# uncomment this if you want to include the code in the image
# as we are always volume mounting this isn't needed and will just increase the size massively
# COPY . .

CMD ["php", "run.php"]