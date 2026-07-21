# Etapa 1: Instalar dependencias PHP con Composer
FROM composer:latest AS composer_builder

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# Etapa 2: Compilar assets de Node / Vite / Vue 3
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./

RUN npm ci

# Copiar el proyecto
COPY . .

# Ziggy necesita esta carpeta durante npm run build
COPY --from=composer_builder /app/vendor ./vendor

RUN npm run build


# Etapa 3: Imagen final PHP 8.3 FPM + Nginx
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    zip \
    unzip \
    git \
    curl \
    bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    bcmath \
    mbstring \
    opcache \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar el proyecto Laravel
COPY . .

# Copiar dependencias PHP ya instaladas
COPY --from=composer_builder /app/vendor ./vendor

# Copiar assets compilados
COPY --from=node_builder /app/public/build ./public/build

RUN composer dump-autoload --optimize

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]