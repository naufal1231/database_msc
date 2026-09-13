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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_entry_id')->unique()->constrained('competition_entries')->onDelete('cascade');
            $table->string('time_result');
            $table->integer('rank')->nullable();
            $table->enum('status', ['finish', 'DQ', 'DNS'])->default('finish');
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
