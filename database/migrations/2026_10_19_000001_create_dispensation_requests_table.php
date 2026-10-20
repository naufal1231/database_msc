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
        Schema::create('dispensation_requests', function (Blueprint $table) {
            $table->id();

            // Relasi ke kompetisi (nama, tanggal, tempat diambil dari tabel ini)
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');

            // Relasi ke atlet
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');

            // Orang tua yang mengajukan (user dengan role 'user')
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');

            // Data sekolah atlet (bukan SwimClass)
            $table->string('school_class');
            $table->string('school_absence_no');

            // Status pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Catatan dari coach jika ditolak (wajib diisi saat reject)
            $table->text('coach_note')->nullable();

            // Coach yang memverifikasi
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            // Path PDF yang telah dibuat (non-public storage)
            $table->string('pdf_path')->nullable();

            $table->timestamps();

            // Unique: satu atlet hanya boleh punya satu pengajuan per kompetisi
            // (jika ditolak dan diajukan ulang, record diupdate, tidak dibuat baru)
            $table->unique(['competition_id', 'athlete_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispensation_requests');
    }
};
