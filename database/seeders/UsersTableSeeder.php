<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        \App\Models\User::create([
            'name' => 'System Administrator',
            'email' => 'admin@myfoodshare.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create a restaurant owner user
        \App\Models\User::create([
            'name' => 'John Restaurant',
            'email' => 'restaurant@myfoodshare.com',
            'password' => bcrypt('restaurant123'),
            'role' => 'restaurant_owner',
            'status' => 'active',
            'restaurant_name' => 'John\'s Italian Restaurant',
            'address' => '123 Main Street, City',
            'phone' => '+1-555-0123',
            'business_license' => 'BL123456',
            'cuisine_type' => 'Italian',
        ]);

        // Create a recipient user
        \App\Models\User::create([
            'name' => 'Jane Charity',
            'email' => 'recipient@myfoodshare.com',
            'password' => bcrypt('recipient123'),
            'role' => 'recipient',
            'status' => 'active',
            'organization_name' => 'City Food Bank',
            'contact_person' => 'Jane Smith',
            'phone' => '+1-555-0456',
            'address' => '456 Charity Ave, City',
            'ngo_registration' => 'NGO789012',
        ]);

        // Create some sample users
        \App\Models\User::factory(10)->create();
    }
}
