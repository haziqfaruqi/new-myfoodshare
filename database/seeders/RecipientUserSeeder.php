<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class RecipientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a recipient user if none exists
        if (!User::where('role', 'recipient')->exists()) {
            User::create([
                'name' => 'Test Recipient',
                'email' => 'recipient@test.com',
                'password' => bcrypt('password'),
                'role' => 'recipient',
                'phone' => '1234567890',
                'address' => '123 Test St, City, State',
                'description' => 'Test recipient account'
            ]);

            $this->command->info('Test recipient user created with:');
            $this->command->info('Email: recipient@test.com');
            $this->command->info('Password: password');
        } else {
            $this->command->info('Recipient user already exists');
        }
    }
}
