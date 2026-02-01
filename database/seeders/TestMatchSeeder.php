<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FoodListing;
use App\Models\RestaurantProfile;
use App\Models\FoodMatch;
use App\Models\PickupVerification;

class TestMatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the test recipient user
        $recipient = User::where('email', 'recipient@test.com')->first();
        if (!$recipient) {
            $this->command->info('Creating test recipient user...');
            $recipient = User::create([
                'name' => 'Test Recipient',
                'email' => 'recipient@test.com',
                'password' => bcrypt('password'),
                'role' => 'recipient',
                'phone' => '1234567890',
                'address' => '123 Test St, City, State',
                'description' => 'Test recipient account'
            ]);
        }

        $this->command->info('Found recipient: ' . $recipient->name);

        // Get or create a restaurant user
        $restaurant = User::where('email', 'restaurant@test.com')->first();
        if (!$restaurant) {
            $restaurant = User::create([
                'name' => 'Test Restaurant',
                'email' => 'restaurant@test.com',
                'password' => bcrypt('password'),
                'role' => 'restaurant',
                'phone' => '1234567890',
                'address' => '123 Restaurant St, City'
            ]);
            $this->command->info('Created restaurant user: ' . $restaurant->name);
        }

        // Create a restaurant profile if it doesn't exist
        if (!$restaurant->restaurantProfile) {
            RestaurantProfile::create([
                'user_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name . ' Restaurant',
                'cuisine_type' => 'other',
                'status' => 'approved',
                'email' => $restaurant->email
            ]);
            $this->command->info('Created restaurant profile');
        }

        // Create test food listing
        $listing = FoodListing::create([
            'user_id' => $restaurant->id,
            'food_name' => 'Test Food Donation',
            'food_type' => 'Main Course',
            'quantity' => 10,
            'unit' => 'servings',
            'expiry_date' => now()->addDays(2),
            'status' => 'approved',
            'pickup_instructions' => 'Please pick up from the back entrance',
            'notes' => 'Freshly prepared, still warm'
        ]);

        $this->command->info('Created food listing: ' . $listing->food_name);

        // Create a match
        $match = FoodMatch::create([
            'food_listing_id' => $listing->id,
            'recipient_id' => $recipient->id,
            'status' => 'approved',
            'pickup_scheduled_at' => now()->addHours(2)
        ]);

        $this->command->info('Created match ID: ' . $match->id);

        // Generate verification code for this match
        $verification = PickupVerification::generateForMatch($match);
        $this->command->info('Created verification code: ' . $verification->verification_code);
        $this->command->info('You can now login with recipient@test.com / password to test verification');
    }
}