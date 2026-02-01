<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register broadcast routes separately to avoid middleware inheritance issues
        // We need auth middleware for session, but exclude role-based middleware
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
