<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Recipient;

class RecipientProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create recipient profiles for all users with recipient role
        User::where('role', 'recipient')->each(function ($user) {
            if (!$user->recipient) {
                $user->recipient()->create([
                    'organization_name' => $user->name . ' Organization',
                    'contact_person' => $user->name,
                    'address' => 'Address to be updated',
                    'capacity' => 50,
                    'dietary_requirements' => [],
                    'status' => 'active',
                    'needs_preferences' => []
                ]);

                echo 'Created recipient profile for: ' . $user->name . PHP_EOL;
            }
        });

        echo 'Recipient profiles created successfully!' . PHP_EOL;
    }
}