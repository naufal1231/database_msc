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
        Schema::create('competition_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->enum('stroke', ['bebas', 'dada', 'kupu', 'punggung', 'ganti', 'estafet']);
            $table->integer('distance');
            $table->enum('category', ['perorangan', 'estafet_mix']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_events');
    }
};
