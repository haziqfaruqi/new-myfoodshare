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
        Schema::table('food_listings', function (Blueprint $table) {
            $table->decimal('estimated_value', 10, 2)->nullable()->after('qr_code_data');
            $table->decimal('estimated_co2_saved', 10, 2)->nullable()->after('estimated_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropColumn(['estimated_value', 'estimated_co2_saved']);
        });
    }
};
