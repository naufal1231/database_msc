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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birth_date');
            $table->enum('gender', ['L', 'P']);
            $table->string('photo')->nullable();
            $table->foreignId('current_class_id')->constrained('classes');
            $table->date('join_date');
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('ku')->storedAs('YEAR(birth_date)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
