FROM php:8.2-apache

# Install extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libpq-dev \
    unzip git curl nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_pgsql pdo_mysql bcmath opcache \
    && a2enmod rewrite

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# PHP config
RUN echo "upload_max_filesize=10M\npost_max_size=12M\nmemory_limit=256M" > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node deps & build
RUN npm ci && npm run build || true

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Create storage link
RUN php artisan storage:link || true

# Start script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 10000

CMD ["docker-entrypoint.sh"]
