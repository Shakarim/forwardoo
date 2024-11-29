# Используем официальный образ PHP 8.2 FPM для локальной разработки
FROM php:8.2-fpm

# Установка рабочей директории
WORKDIR /var/www

# Устанавливаем дополнительные инструменты и пакеты
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libmemcached-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    curl \
    lua-zlib-dev \
    nginx \
    librabbitmq-dev \
    wget \
    libpq-dev \
    libc-client-dev \
    libkrb5-dev

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl

# Копирование файла php.ini-production в php.ini
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# Изменение конфигурационных настроек в php.ini, если это необходимо
RUN sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/post_max_size = 8M/post_max_size = 20M/' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/max_execution_time = 30/max_execution_time = 300/' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = 128M/memory_limit = 256M/' "$PHP_INI_DIR/php.ini"

# Устанавливаем расширения PHP для Laravel
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl gd sockets pdo_pgsql soap imap

RUN docker-php-ext-enable imap

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Очищаем кэш
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY . /var/www

RUN composer install --prefer-dist --no-dev
# Открытие порта 9000
EXPOSE 9000

CMD ["php-fpm"]
