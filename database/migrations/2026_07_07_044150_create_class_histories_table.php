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
        Schema::create('class_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_histories');
    }
};
