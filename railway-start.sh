#!/bin/bash
# Railway startup script - ensure config is fresh

# Clear any cached config
rm -f bootstrap/cache/config.php

# Start the server
php artisan serve --host=0.0.0.0 --port=$PORT
