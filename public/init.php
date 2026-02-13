<?php

// Railway initialization script
require __DIR__ . '/../vendor/autoload.php';

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
    } catch (\Exception $e) {
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
system('php artisan migrate --force', $exitCode);
if ($exitCode !== 0) {
    echo "Migration failed with exit code $exitCode\n";
    // Don't exit on migration failure - might already be migrated
}

echo "Creating storage link...\n";
system('php artisan storage:link', $exitCode);

echo "Caching configs...\n";
system('php artisan config:cache', $exitCode);
system('php artisan route:cache', $exitCode);
system('php artisan view:cache', $exitCode);

echo "Initialization complete!\n";
