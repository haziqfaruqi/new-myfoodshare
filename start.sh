#!/bin/bash

# Run migrations on first deploy
php artisan migrate --force

# Create storage link
php artisan storage:link

# Start the server
php artisan serve --host=0.0.0.0 --port=${PORT}
