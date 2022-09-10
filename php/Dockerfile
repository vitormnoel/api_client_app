FROM php:8.0-fpm

COPY ./php/90-xdebug.ini "${PHP_INI_DIR}/conf.d"

RUN echo "date.timezone=America/Sao_Paulo" > ${PHP_INI_DIR}/conf.d/timezone_sao_paulo.ini
RUN echo "memory_limit = 1024M" > ${PHP_INI_DIR}/conf.d/memory_limit.ini
RUN echo "max_input_vars = 10000" > ${PHP_INI_DIR}/conf.d/maxinputvars.ini

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && pecl install xdebug

WORKDIR /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli