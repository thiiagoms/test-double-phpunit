FROM php:7.4-fpm

RUN apt update && apt install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \ 
    sqlite3 \
    libsqlite3-dev

RUN docker-php-ext-install mbstring exif pcntl bcmath gd sockets pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

USER $user