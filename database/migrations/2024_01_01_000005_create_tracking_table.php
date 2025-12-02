<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['scheduled', 'in_transit', 'delivered', 'delayed', 'cancelled']);
            $table->text('notes')->nullable();
            $table->json('location_data')->nullable();
            $table->timestamp('status_changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking');
    }
};