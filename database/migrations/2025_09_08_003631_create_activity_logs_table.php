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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable(); // Category: donation, pickup, admin, etc.
            $table->text('description'); // Human readable description
            $table->string('subject_type')->nullable(); // Model class name
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->string('event')->nullable(); // created, updated, deleted, etc.
            $table->string('causer_type')->nullable(); // User model
            $table->unsignedBigInteger('causer_id')->nullable(); // User ID
            $table->json('properties')->nullable(); // Additional data
            $table->json('old_values')->nullable(); // Old values for updates
            $table->json('new_values')->nullable(); // New values for updates
            $table->string('batch_uuid')->nullable(); // Group related activities
            $table->timestamp('created_at'); // When it happened
            
            $table->index(['log_name']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['causer_type', 'causer_id']);
            $table->index(['created_at']);
            $table->index(['event']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
