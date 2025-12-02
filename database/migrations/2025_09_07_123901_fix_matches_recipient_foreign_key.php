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
            // Drop the existing foreign key constraint
            $table->dropForeign(['recipient_id']);
            
            // Add the new foreign key constraint to reference users table
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Drop the users foreign key
            $table->dropForeign(['recipient_id']);
            
            // Restore the original recipients foreign key
            $table->foreign('recipient_id')->references('id')->on('recipients')->onDelete('cascade');
        });
    }
};
