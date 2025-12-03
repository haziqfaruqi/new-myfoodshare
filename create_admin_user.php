<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\User;

// Database configuration
$dbConfig = require __DIR__ . '/config/database.php';

// Eloquent setup
$capsule = new DB;
$capsule->addConnection($dbConfig['connections']['mysql']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Check if admin user exists
if (User::where('email', 'admin@myfoodshare.com')->exists()) {
    echo "Admin user already exists.\n";
} else {
    // Create admin user
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@myfoodshare.com',
        'password' => password_hash('password', PASSWORD_BCRYPT),
        'role' => 'admin',
        'status' => 'active',
        'email_verified_at' => now()
    ]);

    echo "Admin user created successfully!\n";
    echo "Email: admin@myfoodshare.com\n";
    echo "Password: password\n";
}