#!/bin/bash

# Wait for database to be ready
echo "Waiting for database connection..."
MAX_RETRIES=30
RETRY_COUNT=0

until php artisan db:show --json >/dev/null 2>&1; do
  RETRY_COUNT=$((RETRY_COUNT + 1))
  if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
    echo "Database connection timeout after $MAX_RETRIES attempts"
    exit 1
  fi
  echo "Database not ready (attempt $RETRY_COUNT/$MAX_RETRIES), waiting..."
  sleep 2
done

echo "Database connected! Running migrations..."
php artisan migrate --force

# Create storage link if not exists
php artisan storage:link

# Clear and cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
