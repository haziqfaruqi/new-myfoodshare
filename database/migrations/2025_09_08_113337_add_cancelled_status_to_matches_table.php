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
        Schema::table('matches', function (Blueprint $table) {
            // Update the status enum to include 'cancelled'
            $table->enum('status', ['pending', 'approved', 'confirmed', 'scheduled', 'in_progress', 'rejected', 'cancelled', 'completed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Revert back to previous enum values
            $table->enum('status', ['pending', 'approved', 'confirmed', 'scheduled', 'in_progress', 'rejected', 'completed'])->default('pending')->change();
        });
    }
};
