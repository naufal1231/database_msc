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
        Schema::create('competition_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('competition_event_id')->constrained('competition_events')->onDelete('cascade');
            $table->enum('status', ['terdaftar', 'DNS', 'DQ'])->default('terdaftar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_entries');
    }
};
