<?php

// Railway initialization script
// This file should be called from startCommand before the main server starts

$retries = 30;
$attempt = 0;

echo "Waiting for database connection...\n";

while ($attempt < $retries) {
    try {
        $pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "Database connected!\n";
        break;
    } catch (PDOException $e) {
        $attempt++;
        if ($attempt >= $retries) {
            echo "Database connection timeout after $retries attempts\n";
            exit(1);
        }
        echo "Database not ready (attempt $attempt/$retries), waiting...\n";
        sleep(2);
    }
}

echo "Running migrations...\n";
passthru('php artisan migrate --force', $exitCode);
if ($exitCode !== 0) {
    echo "Migration failed with exit code $exitCode\n";
    exit($exitCode);
}

echo "Creating storage link...\n";
@mkdir(storage_path('framework/cache'), 0755, true);
@mkdir(storage_path('framework/views'), 0755, true);
@mkdir(storage_path('framework/sessions'), 0755, true);
@mkdir(public_path('storage'), 0755, true);
if (!file_exists(public_path('storage'))) {
    symlink(storage_path('app/public'), public_path('storage'));
}

echo "Caching configs...\n";
passthru('php artisan config:cache', $exitCode);
passthru('php artisan route:cache', $exitCode);
passthru('php artisan view:cache', $exitCode);

echo "Initialization complete!\n";
