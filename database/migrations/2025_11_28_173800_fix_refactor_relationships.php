<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix food_listings table to use restaurant_profile_id instead of user_id
        if (Schema::hasTable('food_listings')) {
            Schema::table('food_listings', function (Blueprint $table) {
                // Drop old foreign key if it exists
                if (Schema::hasColumn('food_listings', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }

                // Add restaurant_profile_id if it doesn't exist
                if (!Schema::hasColumn('food_listings', 'restaurant_profile_id')) {
                    $table->foreignId('restaurant_profile_id')->after('id')->constrained()->onDelete('cascade');
                }

                // Add created_by for audit trail
                if (!Schema::hasColumn('food_listings', 'created_by')) {
                    $table->foreignId('created_by')->after('restaurant_profile_id')->nullable()->constrained('users')->onDelete('set null');
                }
            });
        }

        // Fix matches table relationships
        if (Schema::hasTable('matches')) {
            Schema::table('matches', function (Blueprint $table) {
                // Only add foreign key if it doesn't already exist
                if (!Schema::hasColumn('matches', 'food_listing_id')) {
                    $table->foreignId('food_listing_id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('matches', 'recipient_id')) {
                    $table->foreignId('recipient_id')->constrained()->onDelete('cascade');
                }
            });
        }

        // Fix pickup_verifications table relationships
        if (Schema::hasTable('pickup_verifications')) {
            Schema::table('pickup_verifications', function (Blueprint $table) {
                // Update foreign key constraints to match new structure only if they don't exist
                if (!Schema::hasColumn('pickup_verifications', 'food_match_id')) {
                    $table->foreignId('food_match_id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('pickup_verifications', 'food_listing_id')) {
                    $table->foreignId('food_listing_id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('pickup_verifications', 'recipient_id')) {
                    $table->foreignId('recipient_id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('pickup_verifications', 'donor_id')) {
                    $table->foreignId('donor_id')->constrained()->onDelete('cascade');
                }
            });
        }

        // Migrate existing data from users to restaurant_profiles
        $this->migrateRestaurantData();

        // Migrate existing data from users to recipients
        $this->migrateRecipientData();

        // Update food_listings with restaurant profile IDs
        $this->updateFoodListingRelationships();
    }

    public function down(): void
    {
        // Revert changes (simplified for rollback)
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropForeign(['restaurant_profile_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['restaurant_profile_id', 'created_by']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['food_listing_id']);
            $table->dropForeign(['recipient_id']);
        });

        Schema::table('pickup_verifications', function (Blueprint $table) {
            $table->dropForeign(['food_match_id']);
            $table->dropForeign(['food_listing_id']);
            $table->dropForeign(['recipient_id']);
            $table->dropForeign(['donor_id']);
        });
    }

    private function migrateRestaurantData()
    {
        $users = \App\Models\User::where('role', 'donor')->get();
        foreach ($users as $user) {
            // Check if restaurant profile already exists
            $existingProfile = \App\Models\RestaurantProfile::where('user_id', $user->id)->first();
            if (!$existingProfile) {
                \App\Models\RestaurantProfile::create([
                    'user_id' => $user->id,
                    'restaurant_name' => $user->restaurant_name,
                    'address' => $user->address,
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'description' => $user->description,
                    'business_license' => $user->business_license,
                    'cuisine_type' => $user->cuisine_type,
                    'restaurant_capacity' => $user->restaurant_capacity,
                    'status' => $user->status,
                    'admin_notes' => $user->admin_notes,
                    'approved_at' => $user->approved_at,
                    'approved_by' => $user->approved_by,
                ]);
            }
        }
    }

    private function migrateRecipientData()
    {
        $users = \App\Models\User::where('role', 'recipient')->get();
        foreach ($users as $user) {
            // Check if recipient profile already exists
            $existingRecipient = \App\Models\Recipient::where('user_id', $user->id)->first();
            if (!$existingRecipient) {
                \App\Models\Recipient::create([
                    'user_id' => $user->id,
                    'email' => $user->email ?? $user->name . '@example.com', // Provide default email if null
                    'phone' => $user->phone ?? '',
                    'organization_name' => $user->organization_name ?? $user->name . ' Organization',
                    'contact_person' => $user->contact_person ?? $user->name,
                    'address' => $user->address ?? '',
                    'capacity' => $user->recipient_capacity ?? 100, // Default capacity
                    'dietary_requirements' => $user->dietary_requirements ?? '',
                    'rating' => $user->rating ?? 0,
                    'status' => 'active', // Default to active for existing users
                    'needs_preferences' => $user->needs_preferences ?? 'No specific preferences',
                ]);
            }
        }
    }

    private function updateFoodListingRelationships()
    {
        $foodListings = \App\Models\FoodListing::all();
        foreach ($foodListings as $listing) {
            // Find restaurant profile for this user
            if ($listing->created_by) {
                $user = \App\Models\User::find($listing->created_by);
                if ($user && $user->role === 'donor') {
                    $restaurantProfile = \App\Models\RestaurantProfile::where('user_id', $user->id)->first();
                    if ($restaurantProfile) {
                        $listing->restaurant_profile_id = $restaurantProfile->id;
                        $listing->save();
                    }
                }
            }
        }
    }
};