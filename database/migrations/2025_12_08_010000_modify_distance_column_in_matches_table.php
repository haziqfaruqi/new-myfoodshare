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
            // Change distance column to decimal(10,2) to allow larger values (up to 99,999.99 km)
            $table->decimal('distance', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Revert back to original decimal(5,2) if needed
            $table->decimal('distance', 5, 2)->nullable()->change();
        });
    }
};