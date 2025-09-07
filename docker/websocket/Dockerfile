FROM php:8.4.6-fpm

# Установка зависимостей
RUN apt-get update
RUN apt-get install -y git
RUN apt-get install -y unzip
RUN apt-get install -y libzip-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

# Установка зависимостей
CMD ["php", "composer", "install"]

# Запуск WebSocket сервера
CMD ["php", "bin/console", "app:websocket:serve"]