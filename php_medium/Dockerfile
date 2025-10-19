FROM debian:bookworm-slim

# Configure Russian APT mirrors for faster downloads
RUN sed -i 's|http://deb.debian.org|http://mirror.yandex.ru|g' /etc/apt/sources.list.d/debian.sources && \
    sed -i 's|http://security.debian.org|http://mirror.yandex.ru|g' /etc/apt/sources.list.d/debian.sources

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-zip \
    php8.2-sqlite3 \
    php8.2-gd \
    php8.2-bcmath \
    php8.2-intl \
    sqlite3 \
    curl \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create directory structure
RUN mkdir -p /var/www/app /run/php

# Copy nginx configuration
COPY compose/app/nginx/nginx.conf /etc/nginx/nginx.conf
COPY compose/app/nginx/sites-available/environment.htb /etc/nginx/sites-enabled/environment.htb
COPY compose/app/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copy PHP configuration
COPY compose/app/php/8.2/fpm/php-fpm.conf /etc/php/8.2/fpm/php-fpm.conf
COPY compose/app/php/8.2/fpm/php.ini /etc/php/8.2/fpm/php.ini

# Copy application
COPY compose/app/app/ /var/www/app/

# Set working directory
WORKDIR /var/www/app

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create .env file
RUN cp .env.example .env 2>/dev/null || echo "APP_NAME=Environment\n\
APP_ENV=production\n\
APP_KEY=\n\
APP_DEBUG=false\n\
APP_URL=http://localhost\n\
APP_VERSION=1.0.0\n\
\n\
LOG_CHANNEL=stack\n\
LOG_DEPRECATIONS_CHANNEL=null\n\
LOG_LEVEL=debug\n\
\n\
DB_CONNECTION=sqlite\n\
DB_DATABASE=/var/www/app/database/database.sqlite\n\
\n\
SESSION_DRIVER=file\n\
SESSION_LIFETIME=120\n\
\n\
CACHE_DRIVER=file\n\
QUEUE_CONNECTION=sync" > .env

# Generate application key
RUN php artisan key:generate

# Set permissions
RUN chown -R www-data:www-data /var/www/app \
    && chmod -R 755 /var/www/app/storage \
    && chmod -R 755 /var/www/app/bootstrap/cache \
    && touch /var/www/app/database/database.sqlite \
    && chown www-data:www-data /var/www/app/database/database.sqlite \
    && chmod 664 /var/www/app/database/database.sqlite

# Copy supervisor config
COPY compose/app/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]

