#!/bin/bash
set -e

# Run migrations
php artisan migrate --force 2>/dev/null || true

# Seed if empty
php artisan db:seed --force 2>/dev/null || true

# Cache config
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Fix Apache MPM conflict
a2dismod mpm_event 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Update Apache port
sed -i "s/Listen 80/Listen ${PORT:-10000}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-10000}/g" /etc/apache2/sites-available/*.conf
sed -i "s/*:80/*:${PORT:-10000}/g" /etc/apache2/sites-available/*.conf

# Start Apache
apache2-foreground
