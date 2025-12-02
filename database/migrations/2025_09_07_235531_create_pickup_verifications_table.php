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
        Schema::create('pickup_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('food_listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade');
            $table->string('verification_code')->unique();
            $table->string('qr_code_scanned')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->json('pickup_details')->nullable(); // quantity received, condition, etc.
            $table->enum('verification_status', ['pending', 'verified', 'completed', 'disputed'])->default('pending');
            $table->text('recipient_notes')->nullable();
            $table->text('donor_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('location_data')->nullable(); // GPS coordinates for verification
            $table->json('photo_evidence')->nullable(); // Photos of pickup
            $table->boolean('quality_confirmed')->default(false);
            $table->integer('quality_rating')->nullable(); // 1-5 rating
            $table->text('quality_issues')->nullable();
            $table->timestamp('pickup_completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['verification_code']);
            $table->index(['verification_status']);
            $table->index(['scanned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_verifications');
    }
};
