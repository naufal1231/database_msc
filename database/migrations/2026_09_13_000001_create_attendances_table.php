<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('swim_class_id')->constrained('classes')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'tanpa_keterangan']);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['athlete_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};