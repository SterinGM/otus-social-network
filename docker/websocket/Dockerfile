FROM php:8.4.6-fpm

# Установка зависимостей
RUN apt-get update
RUN apt-get install -y git
RUN apt-get install -y unzip
RUN apt-get install -y libzip-dev
RUN apt-get install -y librabbitmq-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

RUN pecl install -o -f redis
RUN docker-php-ext-enable redis

RUN pecl install amqp
RUN docker-php-ext-enable amqp

# Установка зависимостей
CMD ["php", "composer", "install"]

# Запуск WebSocket сервера
CMD ["php", "bin/console", "app:websocket:serve"]