#!/bin/bash
set -e

# Run migrations
php artisan migrate --force 2>/dev/null || true

# Seed (ignore if already seeded)
php artisan db:seed --force 2>/dev/null || true

# Cache
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Laravel's built-in server
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
