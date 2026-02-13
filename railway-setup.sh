#!/bin/bash
# Run this manually in Railway Console after first deployment
# Usage: Click service → Console tab → paste this and run

echo "Running database migrations..."
php artisan migrate --force

echo "Creating storage link..."
php artisan storage:link

echo "Clearing and caching configs..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setup complete!"
