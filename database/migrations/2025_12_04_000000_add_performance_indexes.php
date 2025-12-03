<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add index to food_listings table for faster queries
        Schema::table('food_listings', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('status');
            $table->index('created_at');
            $table->index('approval_status');
        });

        // Add composite indexes for common query patterns
        Schema::table('food_listings', function (Blueprint $table) {
            $table->index(['created_by', 'status']); // For filtering by owner and status
            $table->index(['created_by', 'created_at']); // For owner's recent listings
            $table->index(['approval_status', 'created_at']); // For pending approvals
        });

        // Add indexes to matches table for faster queries
        Schema::table('food_matches', function (Blueprint $table) {
            $table->index('status');
            $table->index('pickup_scheduled_at');
            $table->index('created_at');
            $table->index('updated_at');
        });

        // Add composite indexes for matches table
        Schema::table('food_matches', function (Blueprint $table) {
            $table->index(['food_listing_id', 'status']); // For specific listing status queries
            $table->index(['status', 'pickup_scheduled_at']); // For scheduling queries
            $table->index(['status', 'created_at']); // For recent activity queries
        });

        // Add index to food_listing_id for faster joins
        Schema::table('food_matches', function (Blueprint $table) {
            $table->index('food_listing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['approval_status']);
            $table->dropIndex(['created_by', 'status']);
            $table->dropIndex(['created_by', 'created_at']);
            $table->dropIndex(['approval_status', 'created_at']);
        });

        Schema::table('food_matches', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['pickup_scheduled_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['food_listing_id', 'status']);
            $table->dropIndex(['status', 'pickup_scheduled_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['food_listing_id']);
        });
    }
};