FROM php:8.4.6-fpm

# Install recommended extensions for Symfony
RUN apt-get update
RUN apt-get install -y libicu-dev
RUN apt-get install -y git
RUN apt-get install -y zip

RUN docker-php-ext-install intl
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

RUN pecl install -o -f redis
RUN docker-php-ext-enable redis

RUN pecl install xdebug-3.4.2
RUN docker-php-ext-enable xdebug

RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer --version=2.8.8

ADD "php.ini" "/usr/local/etc/php/php.ini"