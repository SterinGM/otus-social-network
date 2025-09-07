FROM php:8.4.6-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

# Установка зависимостей
CMD ["php", "composer", "install"]

# Запуск WebSocket сервера
CMD ["php", "bin/console", "app:websocket:serve"]