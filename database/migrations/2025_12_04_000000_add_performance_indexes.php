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
            if (Schema::hasColumn('food_listings', 'created_by')) {
                $table->index('created_by');
            }
            if (Schema::hasColumn('food_listings', 'status')) {
                $table->index('status');
            }
            if (Schema::hasColumn('food_listings', 'created_at')) {
                $table->index('created_at');
            }
            if (Schema::hasColumn('food_listings', 'approval_status')) {
                $table->index('approval_status');
            }
        });

        // Add composite indexes for common query patterns
        Schema::table('food_listings', function (Blueprint $table) {
            if (Schema::hasColumn('food_listings', 'created_by') && Schema::hasColumn('food_listings', 'status')) {
                $table->index(['created_by', 'status']);
            }
            if (Schema::hasColumn('food_listings', 'created_by') && Schema::hasColumn('food_listings', 'created_at')) {
                $table->index(['created_by', 'created_at']);
            }
            if (Schema::hasColumn('food_listings', 'approval_status') && Schema::hasColumn('food_listings', 'created_at')) {
                $table->index(['approval_status', 'created_at']);
            }
        });

        // Add indexes to matches table for faster queries (not food_matches)
        if (Schema::hasTable('matches')) {
            Schema::table('matches', function (Blueprint $table) {
                if (Schema::hasColumn('matches', 'status')) {
                    $table->index('status');
                }
                if (Schema::hasColumn('matches', 'pickup_scheduled_at')) {
                    $table->index('pickup_scheduled_at');
                }
                if (Schema::hasColumn('matches', 'created_at')) {
                    $table->index('created_at');
                }
                if (Schema::hasColumn('matches', 'updated_at')) {
                    $table->index('updated_at');
                }
            });

            // Add composite indexes for matches table
            Schema::table('matches', function (Blueprint $table) {
                if (Schema::hasColumn('matches', 'food_listing_id') && Schema::hasColumn('matches', 'status')) {
                    $table->index(['food_listing_id', 'status']);
                }
                if (Schema::hasColumn('matches', 'status') && Schema::hasColumn('matches', 'pickup_scheduled_at')) {
                    $table->index(['status', 'pickup_scheduled_at']);
                }
                if (Schema::hasColumn('matches', 'status') && Schema::hasColumn('matches', 'created_at')) {
                    $table->index(['status', 'created_at']);
                }
                if (Schema::hasColumn('matches', 'food_listing_id')) {
                    $table->index('food_listing_id');
                }
            });
        }
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

        if (Schema::hasTable('matches')) {
            Schema::table('matches', function (Blueprint $table) {
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
    }
};
