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
        Schema::create('class_transfer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('from_class_id')->constrained('classes');
            $table->foreignId('to_class_id')->constrained('classes');
            $table->date('transfer_date');
            $table->enum('reason', [
                'naik kelas', 'tinggal kelas', 'keluar dari sekolah', 'pindah sekolah',
                'masalah kesehatan', 'alasan keluarga', 'alasan akademik', 'lainnya'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_transfer_logs');
    }
};
