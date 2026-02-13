<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper function to check if index exists
        $indexExists = function($table, $index) {
            $indexes = collect(DB::select("SHOW INDEX FROM {$table}"))->pluck('Key_name')->unique();
            return $indexes->contains($table . '_' . $index . '_index') || $indexes->contains($index);
        };

        // Add index to food_listings table for faster queries
        Schema::table('food_listings', function (Blueprint $table) use ($indexExists) {
            if (Schema::hasColumn('food_listings', 'created_by') && !$indexExists('food_listings', 'created_by')) {
                $table->index('created_by');
            }
            if (Schema::hasColumn('food_listings', 'status') && !$indexExists('food_listings', 'status')) {
                $table->index('status');
            }
            if (Schema::hasColumn('food_listings', 'created_at') && !$indexExists('food_listings', 'created_at')) {
                $table->index('created_at');
            }
            if (Schema::hasColumn('food_listings', 'approval_status') && !$indexExists('food_listings', 'approval_status')) {
                $table->index('approval_status');
            }
        });

        // Add composite indexes for common query patterns
        Schema::table('food_listings', function (Blueprint $table) use ($indexExists) {
            $composite1 = 'food_listings_created_by_status_index';
            if (Schema::hasColumn('food_listings', 'created_by') && Schema::hasColumn('food_listings', 'status')) {
                $indexes = collect(DB::select("SHOW INDEX FROM food_listings"))->pluck('Key_name')->unique();
                if (!$indexes->contains($composite1)) {
                    $table->index(['created_by', 'status']);
                }
            }

            $composite2 = 'food_listings_created_by_created_at_index';
            if (Schema::hasColumn('food_listings', 'created_by') && Schema::hasColumn('food_listings', 'created_at')) {
                $indexes = collect(DB::select("SHOW INDEX FROM food_listings"))->pluck('Key_name')->unique();
                if (!$indexes->contains($composite2)) {
                    $table->index(['created_by', 'created_at']);
                }
            }

            $composite3 = 'food_listings_approval_status_created_at_index';
            if (Schema::hasColumn('food_listings', 'approval_status') && Schema::hasColumn('food_listings', 'created_at')) {
                $indexes = collect(DB::select("SHOW INDEX FROM food_listings"))->pluck('Key_name')->unique();
                if (!$indexes->contains($composite3)) {
                    $table->index(['approval_status', 'created_at']);
                }
            }
        });

        // Add indexes to matches table for faster queries (not food_matches)
        if (Schema::hasTable('matches')) {
            Schema::table('matches', function (Blueprint $table) use ($indexExists) {
                if (Schema::hasColumn('matches', 'status') && !$indexExists('matches', 'status')) {
                    $table->index('status');
                }
                if (Schema::hasColumn('matches', 'pickup_scheduled_at') && !$indexExists('matches', 'pickup_scheduled_at')) {
                    $table->index('pickup_scheduled_at');
                }
                if (Schema::hasColumn('matches', 'created_at') && !$indexExists('matches', 'created_at')) {
                    $table->index('created_at');
                }
                if (Schema::hasColumn('matches', 'updated_at') && !$indexExists('matches', 'updated_at')) {
                    $table->index('updated_at');
                }
            });

            // Add composite indexes for matches table
            Schema::table('matches', function (Blueprint $table) {
                if (Schema::hasColumn('matches', 'food_listing_id') && Schema::hasColumn('matches', 'status')) {
                    $indexes = collect(DB::select("SHOW INDEX FROM matches"))->pluck('Key_name')->unique();
                    if (!$indexes->contains('matches_food_listing_id_status_index')) {
                        $table->index(['food_listing_id', 'status']);
                    }
                }
                if (Schema::hasColumn('matches', 'status') && Schema::hasColumn('matches', 'pickup_scheduled_at')) {
                    $indexes = collect(DB::select("SHOW INDEX FROM matches"))->pluck('Key_name')->unique();
                    if (!$indexes->contains('matches_status_pickup_scheduled_at_index')) {
                        $table->index(['status', 'pickup_scheduled_at']);
                    }
                }
                if (Schema::hasColumn('matches', 'status') && Schema::hasColumn('matches', 'created_at')) {
                    $indexes = collect(DB::select("SHOW INDEX FROM matches"))->pluck('Key_name')->unique();
                    if (!$indexes->contains('matches_status_created_at_index')) {
                        $table->index(['status', 'created_at']);
                    }
                }
                if (Schema::hasColumn('matches', 'food_listing_id')) {
                    $indexes = collect(DB::select("SHOW INDEX FROM matches"))->pluck('Key_name')->unique();
                    if (!$indexes->contains('matches_food_listing_id_index')) {
                        $table->index('food_listing_id');
                    }
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
