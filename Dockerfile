FROM php:8.2-apache

# System deps (git, unzip, libzip) dan PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files dulu untuk caching layer
COPY composer.json composer.lock /var/www/

# Install dependencies PHP (tanpa scripts dulu karena artisan belum ada)
RUN composer install --no-interaction --prefer-dist --no-ansi --no-progress --no-scripts

# Copy sisa source code
COPY . /var/www

# Jalankan scripts setelah source lengkap & optimasi autoload
RUN composer install --no-interaction --prefer-dist --no-ansi --no-progress \
  && composer dump-autoload -o

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]