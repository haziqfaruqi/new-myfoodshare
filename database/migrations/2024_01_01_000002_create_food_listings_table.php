<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('food_name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->integer('quantity');
            $table->string('unit');
            $table->date('expiry_date');
            $table->time('expiry_time')->nullable();
            $table->string('pickup_location');
            $table->text('special_instructions')->nullable();
            $table->json('dietary_info')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'matched', 'picked_up', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_listings');
    }
};