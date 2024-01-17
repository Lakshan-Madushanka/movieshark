FROM devilbox/php-fpm-8.1:latest

ARG user
ARG uid

# Install some helpful packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client

#clean the repostory cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#install laravel required extentions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# install and enable xdebug
RUN pecl install xdebug-3.1.3 \
    && pecl install redis-5.3.7 \
	&& docker-php-ext-enable xdebug redis

RUN docker-php-ext-configure gd --enable-gd --with-jpeg

RUN docker-php-ext-install gd

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# create php.ini file for development
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Set working directory
WORKDIR /var/www/html


