#!/bin/bash

# Run migrations on deployment
php artisan migrate --force

# Create storage link if not exists
php artisan storage:link

# Clear and cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
