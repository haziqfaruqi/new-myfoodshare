<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'restaurant_owner', 'recipient'])->default('recipient');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            
            // Donor (Restaurant) specific fields
            $table->string('restaurant_name')->nullable();
            $table->string('business_license')->nullable();
            $table->string('cuisine_type')->nullable();
            $table->integer('restaurant_capacity')->nullable();
            
            // Recipient (NGO) specific fields
            $table->string('organization_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('ngo_registration')->nullable();
            $table->json('dietary_requirements')->nullable();
            $table->integer('recipient_capacity')->nullable();
            $table->text('needs_preferences')->nullable();
            
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};